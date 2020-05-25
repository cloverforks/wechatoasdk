<?php

namespace CloverPHP\WechatOANotice;

use Exception;

/**
 * 对公众平台发送给公众账号的消息加解密示例代码.
 *
 * @copyright Copyright (c) 1998-2014 Tencent Inc.
 */

/**
 * 1.第三方回复加密消息给公众平台；
 * 2.第三方收到公众平台发送的消息，验证消息的安全性，并对消息进行解密。
 */
class WxAesMsg
{
    private $token;
    private $encodingAesKey;
    private $appId;

    /**
     * 构造函数
     * @param $token string 公众平台上，开发者设置的token
     * @param $appId string 公众平台的appId
     * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
     */
    public function __construct($token, $appId, $encodingAesKey)
    {
        $this->token = $token;
        $this->appId = $appId;
        $this->encodingAesKey = $encodingAesKey;
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
     * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
     *                      当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function encryptMsg($replyMsg, $timeStamp, $nonce, &$encryptMsg)
    {
        $pc = new WxAesCrypt($this->encodingAesKey, $this->appId);

        //加密
        $array = $pc->encrypt($replyMsg);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        if ($timeStamp == null) {
            $timeStamp = time();
        }
        $encrypt = $array[1];

        //生成安全签名
        $array = $this->getSignature($this->token, $timeStamp, $nonce, $encrypt);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }
        $signature = $array[1];

        //生成发送的xml
        $encryptMsg = $this->generateEncrypted($encrypt, $signature, $timeStamp, $nonce);
        return WxCode::$OK;
    }


    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param $msgSignature string 签名串，对应URL参数的msg_signature
     * @param $timestamp string 时间戳 对应URL参数的timestamp
     * @param $nonce string 随机串，对应URL参数的nonce
     * @param $postData string 密文，对应POST请求的数据
     * @param &$msg string 解密后的原文，当return返回0时有效
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptMsg($msgSignature, $timestamp, $nonce, $postData, &$msg)
    {
        if (strlen($this->encodingAesKey) != 43)
            return WxCode::$IllegalAesKey;

        $pc = new WxAesCrypt($this->encodingAesKey, $this->appId);

        //提取密文
        $array = $this->extractEncrypted($postData);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        $encrypt = $array[1];
        //$touser_name = $array[2];

        //验证安全签名
        $array = $this->getSignature($this->token, $timestamp, $nonce, $encrypt);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $msgSignature) {
            return WxCode::$ValidateSignatureError;
        }

        $result = $pc->decrypt($encrypt);
        if ($result[0] != 0) {
            return $result[0];
        }
        $msg = $result[1];

        return WxCode::$OK;
    }

    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt 密文消息
     * @return array
     */
    private function getSignature($token, $timestamp, $nonce, $encrypt)
    {
        //排序
        try {
            $array = array($encrypt, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return array(WxCode::$OK, sha1($str));
        } catch (Exception $e) {
            //print $e . "\n";
            return array(WxCode::$ComputeSignatureError, null);
        }
    }


    /**
     * 提取出xml数据包中的加密消息
     * @param string $xmlText 待提取的xml字符串
     * @return array 提取出的加密消息字符串
     */
    private function extractEncrypted($xmlText)
    {
        try {
            libxml_disable_entity_loader(true);
            $postArr = (array)simplexml_load_string($xmlText, 'SimpleXMLElement', LIBXML_NOCDATA);
            $encrypt = isset($postArr['Encrypt']) ? $postArr['Encrypt'] : '';
            $tousername = isset($postArr['ToUserName']) ? $postArr['ToUserName'] : '';
            return array(0, $encrypt, $tousername);
        } catch (Exception $e) {
            //print $e . "\n";
            return array(WxCode::$ParseXmlError, null, null);
        }
    }

    /**
     * 生成xml消息
     * @param string $encrypt 加密后的消息密文
     * @param string $signature 安全签名
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @return string
     */
    private function generateEncrypted($encrypt, $signature, $timestamp, $nonce)
    {
        $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }
}
