# nCoV-push

nCoV疫情实时播报推送脚本。数据基于丁香园。

## 使用说明

### 环境要求

- php7 以上
- 当前目录可写

### 推送配置

#### Server酱

第一行，将「"填写SendKey"」换成你的 sendkey 即可。
```
define("SCKEY", "填写SendKey");
```

#### 其他

在42行处，添加推送函数即可，`$title` 为标题，`$content` 为内容。

```
print_r(sc_send($title, $content, SCKEY));
```

### 定时访问

然后定时访问本页面即可。可以通过 cron 、云平台的定时任务等方式操作。

```
*/5 * * * * /usr/local/bin/php cron.php >/dev/null 2>&1
```

注意丁香园的页面因为访问频繁且长，经常出现一半内容返回的情况，这时候提取函数会失败。要考虑重试。