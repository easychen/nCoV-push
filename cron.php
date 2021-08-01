<?php
define("SCKEY", "填写SendKey");

@date_default_timezone_set('Asia/Chongqing');

// 设置勿扰时间
$hour = intval(date("H"));
if ($hour < 8 || $hour > 23) {
    echo "夜间和清晨时段不推送";
    exit;
}

// 获取最新播报信息
if ($newdata = get_nCoV_news()) {
    if (isset($newdata[0]['id']) && intval($newdata[0]['id']) > 0) {
        
        // 本次的信息
        $newid = intval($newdata[0]['id']);
        $news = $newdata[0];
        
        // 上次的信息
        $lastid = intval(kget("lastid"));
        if ($lastid < $newid) {
            // 相同标题，每小时只推送一次
            $nowstring  = date("Y-m-d-H") .'-'.$news['title']; // 按小时的唯一标题
            
            if (kget("laststring") == $nowstring) {
                echo "本小时已经发送过同样标题的内容".$nowstring;
                exit;
            }
            
            // 保存特征值
            kset("lastid", $newid);
            kset("laststring", $nowstring);
            
            // 准备推送内容
            $title = $news['id'].'.'.$news['title'];
            $content = $news['summary'] . "\r\n\r\n --- \r\n\r\n ⚠️ 如发现标题编号不连续，请点击下边疫情页确认可能错过的播报。 \r\n\r\n 💊 [消息源:" . $news['infoSource'] . "](" . $news['sourceUrl'] . ")  💊 [丁香园疫情页](https://3g.dxy.cn/newh5/view/pneumonia) ";
            
            // 推送
            // 可以通过 server酱，或者其他接口（如钉钉、短信、邮件）进行推送
            print_r(sc_send($title, $content, SCKEY));
        } else {
            echo "没有新的数据,lastid {$lastid}  newid {$newid}, 最新记录 ".print_r($news, 1);
        }
    } else {
        echo "数据格式异常";
        print_r($newdata);
    }
} else {
    echo "获取数据失败";
}


// ========================
// 以下用到的函数

function kset($key, $value)
{
    $data = @json_decode(file_get_contents('data.json'), true);
    $data[md5($key)] = $value;
    file_put_contents('data.json', json_encode($data));
}

function kget($key)
{
    $data = @json_decode(file_get_contents('data.json'), true);
    return isset($data[md5($key)]) ? $data[md5($key)] : false;
}

function get_nCoV_news()
{
    $reg = '/<script id="getTimelineService1">.+?window.getTimelineService1\s=\s(\[{.+?\])}catch\(e\){}<\/script>/im';
    if (preg_match($reg, $content = file_get_contents('https://3g.dxy.cn/newh5/view/pneumonia'), $out)) {
        return @json_decode($out[1], 1);
    } else {
        echo "页面内容" . $content;
    }
    return false;
}

function sc_send($text, $desp = '', $key = '')
{
    $postdata = http_build_query(
        array(
        'text' => $text,
        'desp' => $desp
    )
    );

    $opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);
    $context  = stream_context_create($opts);
    return $result = file_get_contents('https://sc.ftqq.com/'.$key.'.send', false, $context);
}
