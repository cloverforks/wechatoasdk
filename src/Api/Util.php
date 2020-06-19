<?php


namespace Clover\WechatOA\Api;


/**
 * Class User
 */
class Util
{
    private $accessToken;
    private $linkUrl = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=%s';
    private $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=%s';
    private $apiIpUrl = 'https://api.weixin.qq.com/cgi-bin/get_api_domain_ip?access_token=%s';

    /**
     * Media constructor.
     * @param string $accessToken
     */
    final public function __construct(&$accessToken)
    {
        $this->accessToken = &$accessToken;
    }

    public function getApiIp()
    {
        $url = sprintf($this->apiIpUrl, $this->accessToken);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }


    /**
     * @return array|bool
     */
    public function getWechatIp()
    {
        $url = sprintf($this->wechatUrl, $this->accessToken);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }

    /**
     * @param string $longUrl
     * @return array|bool
     */
    public function long2short($longUrl)
    {
        $url = sprintf($this->linkUrl, $this->accessToken);
        $params = [
            'action' => 'long2short',
            'long_url' => $longUrl
        ];
        if ($result = wechat_post_request($url, $params, true))
            return $result;
        return false;
    }
}