<?php

use Clover\WechatOA\WxCallback;
use Clover\WechatOA\WxXml;

require __DIR__ . '/vendor/autoload.php';

$config = [//这4个参数可以在公众号后台找到
    'token' => 'your_token',
    'appid' => 'your_app_id',
    'appsecret' => 'your_app_secret',
    'encodingAESKey' => 'your_aes_key'
];

$callback = new WxCallback($config);
$params = $callback->getParam();
$xml = new WxXml($params);
$string = $xml->generateText($callback);
if ($callback->encrypt_type === 'aes')
    $callback->encrypt($string);
echo $string;