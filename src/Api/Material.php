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

    //
    private $addNewsUrl = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=%s';
    private $updateNewsUrl = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=%s';
    private $addNewsImgUrl = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=%s';

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
                'introduction' => $introduction,
            ]),
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

    public function addNewsImg($file)
    {
        $url = sprintf($this->addNewsImgUrl, $this->accessToken);
        if ($result = wechat_upload_file($url, $file, 'image'))
            return $result;
        return false;
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
    public function deleteMaterial($mediaId)
    {
        $url = sprintf($this->delUrl, $this->accessToken);
        if ($result = wechat_post_request($url, ['media_id' => $mediaId]))
            return $result;
        return false;
    }


    /**
     * 获取图文详情
     * @param string $mediaId
     * @return string|bool
     */
    public function getNews($newsId)
    {
        $url = sprintf($this->getUrl, $this->accessToken);
        if ($result = wechat_post_request($url, ['media_id' => $newsId]))
            return $result;
        return false;
    }


    /**
     * 获取永久素材统计
     * @return string|bool
     */
    public function getMaterialCount()
    {
        $url = sprintf($this->countUrl, $this->accessToken);
        if ($result = wechat_get_request($url))
            return $result;
        return false;
    }


    /**
     * 下载永久素材
     * @param $mediaId
     * @param $file
     * @return bool|false|int|string
     */
    public function downloadMaterial($mediaId, $file)
    {
        $url = sprintf($this->getUrl, $this->accessToken);
        if ($result = wechat_download_file($url, $file, ['media_id' => $mediaId]))
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

    /**
     * @param $author
     * @param $title
     * @param $content
     * @param $sourceUrl
     * @param $thumbId
     * @param string $digest
     * @param int $showcoverPic
     * @param int $allowComment
     * @param int $onlyFansComment
     * @return bool|mixed
     */
    public function addNews($author, $title, $content, $sourceUrl, $thumbId, $digest = '', $showcoverPic = 1, $allowComment = 1, $onlyFansComment = 0)
    {
        $url = sprintf($this->addNewsUrl, $this->accessToken);
        $params = $this->createNews($author, $title, $content, $sourceUrl, $thumbId, $digest, $showcoverPic, $allowComment, $onlyFansComment);
        if ($result = wechat_post_request($url, $params))
            return $result;
        return false;
    }

    /**
     * @param $meadiaId
     * @param $author
     * @param $title
     * @param $content
     * @param $sourceUrl
     * @param $thumbId
     * @param string $digest
     * @param int $showcoverPic
     * @param int $index
     * @return bool|mixed
     */
    public function updateNews($meadiaId, $author, $title, $content, $sourceUrl, $thumbId, $digest = '', $showcoverPic = 1, $index = 0)
    {
        $url = sprintf($this->updateNewsUrl, $this->accessToken);
        $params = $this->createNews($author, $title, $content, $sourceUrl, $thumbId, $digest = '', $showcoverPic = 1);
        $params = [
            'media_id' => $meadiaId,
            'index' => $index,
            'articles' => $params
        ];
        if ($result = wechat_post_request($url, $params))
            return $result;
        return false;
    }

    /**
     * @param $author
     * @param $title
     * @param $content
     * @param $sourceUrl
     * @param $thumbId
     * @param string $digest
     * @param int $showcoverPic
     * @param int|null $allowComment
     * @param int|null $onlyFansComment
     * @return array
     */
    private function createNews($author, $title, $content, $sourceUrl, $thumbId, $digest = '', $showcoverPic = 1, $allowComment = null, $onlyFansComment = null)
    {
        $news = [
            'title' => $title,//标题
            'thumb_media_id' => $thumbId,//图文消息的封面图片素材id（必须是永久mediaID）
            'author' => $author,//作者
            'digest' => $digest,//图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。如果本字段为没有填写，则默认抓取正文前64个字。
            'show_cover_pic' => $showcoverPic,//是否显示封面，0为false，即不显示，1为true，即显示
            'content' => $content,//图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源 "上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。
            'content_source_url' => $sourceUrl,//图文消息的原文地址，即点击“阅读原文”后的URL
//            'need_open_comment' => $allowComment,//是否打开评论，0不打开，1打开
//            'only_fans_can_comment' => $onlyFansComment,//是否粉丝才可评论，0所有人可评论，1粉丝才可评论
        ];

        if ($allowComment !== null) $news['need_open_comment'] = $allowComment;
        if ($onlyFansComment !== null) $news['need_open_comment'] = $onlyFansComment;

    }
}