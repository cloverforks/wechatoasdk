<?php


namespace Clover\WechatOA\Api;


/**
 * Class Menu
 */
class Menu
{
    private $accessToken;
    private $getUrl = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=%s';
    private $createUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s';
    private $deleteUrl = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s';

    /**
     * Media constructor.
     * @param string $accessToken
     */
    final public function __construct(&$accessToken)
    {
        $this->accessToken = &$accessToken;
    }

    /**
     * 获取菜单列表
     * @return bool|mixed
     */
    public function getMenus()
    {
        $url = sprintf($this->getUrl, $this->accessToken);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }

    /**
     * 创建菜单
     * @param array $params
     * @return bool|mixed
     */
    public function updateMenus($params)
    {
        $url = sprintf($this->createUrl, $this->accessToken);
        if ($result = wechat_post_request($url, $params, true))
            return $result;
        return false;
    }

    /**
     * 删除菜单
     * @return string|bool
     */
    public function deleteMenu()
    {
        $url = sprintf($this->deleteUrl, $this->accessToken);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }
}