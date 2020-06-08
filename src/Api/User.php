<?php


namespace Clover\WechatOA\Api;


/**
 * Class User
 */
class User
{
    private $accessToken;
    private $getUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=$s';
    private $listUrl = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid=%s';
    private $setBlacklistUrl = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token=%s';

    /**
     * Media constructor.
     * @param string $accessToken
     */
    final public function __construct(&$accessToken)
    {
        $this->accessToken = &$accessToken;
    }

    /**
     * @param $openid
     * @param string $lang
     * @return array|bool
     */
    public function getInfo($openid, $lang = 'zh_CN')
    {
        $url = sprintf($this->getUrl, $this->accessToken, $openid, $lang);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }

    /**
     * 用户列表
     * @param string $nextOpenid
     * @return array|bool
     */
    public function getList($nextOpenid = '')
    {
        $url = sprintf($this->listUrl, $this->accessToken, $nextOpenid);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }

    /**
     * 加入黑名单
     * @param $openids
     * @return bool|mixed
     */
    public function addToBlacklist($openids)
    {
        $params = is_array($openids) ? $openids : [$openids];
        $url = sprintf($this->setBlacklistUrl, $this->accessToken);
        if ($result = wechat_post_request($url, $params,true))
            return $result;
        return false;
    }
}