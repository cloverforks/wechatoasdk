<?php


namespace Clover\WechatOA\Api;


/**
 * Class Media
 * Enhanced curl and make more easy to use
 */
class Media
{
    private $accessToken;
    private $getUrl = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&media_id=%s';
    private $addUrl = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=%s';

    /**
     * Media constructor.
     * @param string $accessToken
     */
    final public function __construct(&$accessToken)
    {
        $this->accessToken = &$accessToken;
    }

    /**
     * @param $mediaId
     * @return bool|mixed
     */
    public function getMedia($mediaId)
    {
        $url = sprintf($this->getUrl, $this->accessToken, $mediaId);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }
}