<?php


namespace Clover\WechatOA\Api;


/**
 * Class Media
 */
class QR
{
    private $accessToken;
    private $createUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
    private $downloadUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s';

    /**
     * Media constructor.
     * @param string $accessToken
     */
    final public function __construct(&$accessToken)
    {
        $this->accessToken = &$accessToken;
    }

    /**
     * 创建临时二维码
     * @param int|string $scene
     * @param int $expired
     * @return bool|mixed
     */
    public function createQR($scene, $expired = 604800)
    {
        $url = sprintf($this->createUrl, $this->accessToken);
        $action = is_string($scene) ? 'QR_STR_SCENE' : 'QR_SCENE';
        $params = [
            'action_name' => $action,
            'expire_seconds' => $expired,
            'action_info' => []
        ];
        $params['action_info']['scene'] = $action === 'QR_SCENE' ? ['scene_id' => $scene] : ['scene_str' => $scene];
        if ($result = wechat_post_request($url, $params, true))
            return $result;
        return false;
    }

    /**
     * 创建永久二维码
     * @param int|string $scene
     * @return bool|mixed
     */
    public function createLimitQR($scene)
    {
        $url = sprintf($this->createUrl, $this->accessToken);
        $action = is_string($scene) ? 'QR_LIMIT_STR_SCENE' : 'QR_LIMIT_SCENE';
        $params = [
            'action_name' => $action,
            'action_info' => [],
        ];
        $params['action_info']['scene'] = $action === 'QR_LIMIT_SCENE' ? ['scene_id' => $scene] : ['scene_str' => $scene];
        if ($result = wechat_post_request($url, $params, true))
            return $result;
        return false;
    }

    /**
     * 下载二维码
     * @param string $ticket
     * @param string|true $file
     * @return string|bool
     */
    public function downloadQR($ticket, $file)
    {
        $url = sprintf($this->downloadUrl, $ticket);
        if ($result = wechat_download_file($ticket, $file))
            return $result;
        return false;
    }
}