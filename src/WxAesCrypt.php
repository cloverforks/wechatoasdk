<?php

namespace Clover\WechatOA;

use Exception;

/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
final class WxAesCrypt
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $appId;

    /**
     * WxCrypt constructor.
     * @param string $key
     * @param string $appId
     */
    function __construct($key, $appId = '')
    {
        $this->key = base64_decode($key . "=");
        $this->appId = $appId;
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return array 加密后的密文
     */
    public function encrypt($text)
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $iv = substr($this->key, 0, 16);
            $text = $random . pack("N", strlen($text)) . $text . $this->appId;
            $text = $this->PKCS7Padding($text, 16);
            $encrypted = openssl_encrypt($text, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
            return array(WxCode::$OK, base64_encode($encrypted));
        } catch (Exception $e) {
            //print $e;
            return array(WxCode::$EncryptAESError, null);
        }
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return array 解密得到的明文
     */
    public function decrypt($encrypted)
    {
        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $iv = substr($this->key, 0, 16);
            $result = openssl_decrypt($ciphertext_dec, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        } catch (Exception $e) {
            return array(WxCode::$DecryptAESError, null);
        }

        try {
            //去除补位字符
            $result = $this->PKCS7UnPadding($result);
            if (strlen($result) < 16)
                return array(WxCode::$IllegalBuffer, null);

            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appId = substr($content, $xml_len + 4);
        } catch (Exception $e) {
            return array(WxCode::$IllegalBuffer, null);
        }

        if ($from_appId != $this->appId)
            return array(WxCode::$ValidateAppidError, null);
        return array(0, $xml_content);
    }

    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    private function getRandomStr()
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

    private function PKCS7Padding($str, $block_size)
    {
        $padding_char = $block_size - (strlen($str) % $block_size);
        $padding_str = str_repeat(chr($padding_char), $padding_char);
        return $str . $padding_str;
    }

    private function PKCS7UnPadding($str)
    {
        $char = substr($str, -1, 1);
        $num = ord($char);
        if ($num > 0 && $num <= strlen($str)) {
            $str = substr($str, 0, -1 * $num);
        }
        return $str;
    }


    private function ZeroPadding($str, $block = 16)
    {
        $pad = $block - (strlen($str) % $block);
        if ($pad == $block) return $str;
        return $str . str_repeat(chr(0), $pad);
    }

    private function ZeroUnPadding($str)
    {
        return rtrim($str, "\0");
    }
}