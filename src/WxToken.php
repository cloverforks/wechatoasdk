<?php


namespace Clover\WechatOA;


/**
 * Class WxToken
 * @property-read string $accessToken
 * @property-read int $accessExpired
 */
class WxToken
{
    /**
     * @var string
     */
    private $appId;
    /**
     * @var string
     */
    private $appSecret;
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var int
     */
    private $accessExpired;
    /**
     * @var string
     */
    private $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';

    /**
     * WxToken constructor.
     * @param $appId
     * @param $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * @return bool|array
     */
    public function request()
    {
        $url = sprintf($this->url, $this->appId, $this->appSecret);
        if ($data = wechat_get_request($url)) {
            $this->accessToken = $data['access_token'];
            $this->accessExpired = time() + $data['expires_in'];
            return $data;
        }
        return false;
    }

    /**
     * @param $name
     * @return int|string|null
     */
    public function __get($name)
    {
        if (isset($this->$name))
            return $this->$name;
        return null;
    }
}