<?php

namespace Clover\WechatOA;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Class WxIndex
 * @property-read string $signature
 * @property-read string $timestamp
 * @property-read string $nonce
 * @property-read string $openid
 * @property-read string $echostr
 * @property-read string $encrypt_type
 * @property-read string $msg_signature
 * @property-read string $token
 * @property-read string $appId
 * @property-read string $appSecret
 * @property-read string $encodingAesKey
 */
class WxCallback
{

    private $signature;

    private $timestamp;

    private $nonce;

    private $openid;

    private $echostr;

    private $encrypt_type;

    private $msg_signature;

    private $token;

    private $appId;

    private $appSecret;

    private $encodingAesKey;

    /**
     * @var WxAesMsg
     */
    private $aesMsg;

    /**
     * @var array
     */
    private $params = [];

    /**
     * WxIndex constructor.
     * @param array $params
     * @param Request|null $request
     * @param Response|null $response
     */
    public function __construct(array $params, Request $request = null, Response $response = null)
    {
        $this->token = isset($params['token']) ? (string)$params['token'] : '';
        $this->appId = isset($params['appid']) ? (string)$params['appid'] : '';
        $this->appSecret = isset($params['appsecret']) ? (string)$params['appsecret'] : '';
        $this->encodingAesKey = isset($params['encodingAESKey']) ? (string)$params['encodingAESKey'] : '';

        if ($request instanceof Request) {
            $this->signature = isset($request->get['signature']) ? $request->get['signature'] : '';
            $this->timestamp = isset($request->get['timestamp']) ? $request->get['timestamp'] : '';
            $this->nonce = isset($request->get['nonce']) ? $request->get['nonce'] : '';
            $this->openid = isset($request->get['openid']) ? $request->get['openid'] : '';
            $this->echostr = isset($request->get['echostr']) ? $request->get['echostr'] : '';
            $this->encrypt_type = isset($request->get['encrypt_type']) ? $request->get['encrypt_type'] : '';
            $this->msg_signature = isset($request->get['msg_signature']) ? $request->get['msg_signature'] : '';
            $result = $this->checkSignature();
            if (false === $result)
                throw new \RuntimeException('wechatoa_invalid_signature');
            elseif (is_string($result))
                $response->end($result);
        } else {
            $this->signature = isset($_GET["signature"]) ? (string)$_GET['signature'] : '';
            $this->timestamp = isset($_GET["timestamp"]) ? (string)$_GET['timestamp'] : '';
            $this->nonce = isset($_GET["nonce"]) ? (string)$_GET['nonce'] : '';
            $this->openid = isset($_GET["openid"]) ? (string)$_GET['openid'] : '';
            $this->echostr = isset($_GET["echostr"]) ? (string)$_GET['echostr'] : '';
            $this->encrypt_type = isset($_GET["encrypt_type"]) ? (string)$_GET['encrypt_type'] : '';
            $this->msg_signature = isset($_GET["msg_signature"]) ? (string)$_GET['msg_signature'] : '';
            if (!$this->checkSignature())
                throw new \RuntimeException('wechatoa_invalid_signature');
        }


        $postString = $request instanceof Request ? $request->rawContent() : file_get_contents("php://input");
        if ($this->encrypt_type === 'aes') {
            $this->aesMsg = new WxAesMsg($this->token, $this->appId, $this->encodingAesKey);
            if (0 !== $this->aesMsg->decryptMsg($this->msg_signature, $this->timestamp, $this->nonce, $postString, $postString))
                throw new \RuntimeException('wechatoa_invalid_callback');
        }

        libxml_disable_entity_loader(true);
        $this->params = (array)simplexml_load_string($postString, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function encrypt(&$text)
    {
        $this->aesMsg->encryptMsg($text, time(), $this->nonce, $text);
        return $text;
    }

    /**
     * @param string|null $name
     * @param mixed $default
     * @return array|mixed|null
     */
    public function getParam($name = null, $default = null)
    {
        if ($name === null)
            return $this->params;
        elseif (isset($this->params[$name]))
            return $this->params[$name];
        else
            return $default;
    }

    /**
     * @return bool
     */
    public function checkSignature()
    {
        $tmpArr = array($this->token, $this->timestamp, $this->nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $signature = sha1($tmpStr);

        if ($signature === $this->signature) {
            if (!empty($this->echostr))
                return $this->echostr;
            else
                return true;
        } else {
            return false;
        }
    }

    public function __toString()
    {
        return json_encode(get_object_vars($this));
    }
}