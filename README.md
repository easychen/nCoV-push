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