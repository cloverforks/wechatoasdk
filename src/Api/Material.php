<?php


namespace Clover\WechatOA\Api;


/**
 * Class Media
 */
class Material
{
    private $accessToken;
    private $getUrl = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=%s';
    private $addUrl = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=%s&type=%s';
    private $delUrl = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=%s';
    private $countUrl = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=%s';
    private $listUrl = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=%s';

    /**
     * Media constructor.
     * @param string $accessToken
     */
    final public function __construct(&$accessToken)
    {
        $this->accessToken = &$accessToken;
    }

    /**
     * @param $file
     * @return array|bool
     */
    public function addImage($file)
    {
        return $this->addMaterial($file, 'image');
    }


    /**
     * @param $file
     * @return array|bool
     */
    public function addThumb($file)
    {
        return $this->addMaterial($file, 'thumb');
    }

    /**
     * @param $file
     * @return array|bool
     */
    public function addVoice($file)
    {
        return $this->addMaterial($file, 'voice');
    }


    /**
     * @param $file
     * @param string $title
     * @param string $introduction
     * @return array|bool
     */
    public function addVideo($file, $title = '', $introduction = '')
    {
        $data = [
            'type' => 'video',
            'description' => json_encode([
                'title' => $title,
                'intro' => $introduction,
                'introduction' => $introduction,
                'description' => $introduction,
                'desc' => $introduction,
            ])
        ];
        return $this->addMaterial($file, $data);
    }

    /**
     * @param $offset
     * @param $count
     * @return bool|mixed
     */
    public function getImageList($offset, $count)
    {
        return $this->getMaterialList('image', $offset, $count);
    }

    /**
     * @param $offset
     * @param $count
     * @return bool|mixed
     */
    public function getThumbList($offset, $count)
    {
        return $this->getMaterialList('thumb', $offset, $count);
    }

    /**
     * @param $offset
     * @param $count
     * @return bool|mixed
     */
    public function getVoiceList($offset, $count)
    {
        return $this->getMaterialList('voice', $offset, $count);
    }

    /**
     * @param $offset
     * @param $count
     * @return bool|mixed
     */
    public function getVideoList($offset, $count)
    {
        return $this->getMaterialList('video', $offset, $count);
    }

    /**
     * 新增临时素材
     * @param array $file
     * @param string $type image/video/voice/thumb
     * @return array|bool
     */
    private function addMaterial($file, $type = '')
    {
        $url = sprintf($this->addUrl, $this->accessToken, is_array($type) ? 'video' : $type);
        if ($result = wechat_upload_file($url, $file, $type))
            return $result;
        return false;
    }

    /**
     * 删除永久素材
     * @param string $mediaId
     * @return string|bool
     */
    private function deleteMaterial($mediaId)
    {
        $url = sprintf($this->delUrl, $this->accessToken);
        if ($result = wechat_post_request($url, ['media_id' => $mediaId]))
            return $result;
        return false;
    }


    /**
     * 获取永久素材
     * @param string $mediaId
     * @return string|bool
     */
    public function getMaterial($mediaId)
    {
        $url = sprintf($this->getUrl, $this->accessToken, $mediaId);
        if ($result = wechat_post_request($url, ['media_id' => $mediaId]))
            return $result;
        return false;
    }


    /**
     * 获取永久素材统计
     * @return string|bool
     */
    private function getMaterialCount()
    {
        $url = sprintf($this->countUrl, $this->accessToken);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }

    /**
     * 获取永久素材列表
     * @param $type
     * @param $offset
     * @param $count
     * @return bool|mixed
     */
    private function getMaterialList($type, $offset, $count)
    {
        $url = sprintf($this->listUrl, $this->accessToken);
        $params = [
            'type' => $type,
            'offset' => $offset,
            'count' => $count,
        ];
        if ($result = wechat_post_request($url, $params))
            return $result;
        return false;
    }
}