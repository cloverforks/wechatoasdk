# 微信公众号通知SDK

这个SDK实现了微信公众号通知的地址验证，消息校验和XML解析。透明支持明文模式和安全模式，具体参考下面的示例。

## 说明

[微信官方有提供相关的sdk](https://wximg.gtimg.com/shake_tv/mpwiki/cryptoDemo.zip)，不过里面的PHP示例的功能有限，代码也比较一般，所以干脆重新改写一翻吧。

## 依赖

* php5.3+
* json扩展
* openssl扩展
* libxml扩展
* simplexml扩展

## 示例

创建一个php文件，比如 `index.php`: 

```php
<?php
use CloverPHP\WechatOANotice\WxCallback;
use CloverPHP\WechatOANotice\WxXml;

require __DIR__ . '/vendor/autoload.php';

$config = [//这4个参数可以在公众号后台找到
    'token' => 'your_token',
    'appid' => 'your_app_id',
    'appsecret' => 'your_app_secret',
    'encodingAESKey' => 'your_aes_key'
];
$callback = new WxCallback($config);//这里会自动处理接口地址验证和解析微信参数，如果不合法会直接中断
$params = $callback->getParam();//上面一行解析完，这里通过getParam可以获取XML对应的参数

$xml = new WxXml($params);
$string = $xml->generateText($callback);
if ($callback->encrypt_type === 'aes')
    $callback->encrypt($string);
echo $string;
```