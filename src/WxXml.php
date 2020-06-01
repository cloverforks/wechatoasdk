<?php

namespace CloverPHP\WechatOANotice;

final class WxXml
{
    private $data = '';
    private $msgType = '';
    private $toUser = '';
    private $fromUser = '';
    private $template = '<xml>
<ToUserName><![CDATA[{toUser}]]></ToUserName>
<FromUserName><![CDATA[{fromUser}]]></FromUserName>
<CreateTime>{createTime}</CreateTime>
<MsgType><![CDATA[{msgType}]]></MsgType>
{content}
</xml>';

    /**
     * WechatResponse constructor.
     * @param string|array $oa
     * @param string $openid
     */
    public function __construct($oa, $openid = '')
    {
        if (is_array($oa)) {
            $this->toUser = isset($oa['FromUserName']) ? $oa['FromUserName'] : '';
            $this->fromUser = isset($oa['ToUserName']) ? $oa['ToUserName'] : '';
        } else {
            $this->toUser = $openid;
            $this->fromUser = $oa;
        }
    }

    /**
     * 返回文本消息给用户
     * @param string $content
     * @return string
     */
    public function genText($content)
    {
        $this->msgType = 'text';
        $this->data = "<Content><![CDATA[{$content}]]></Content>";
        return $this->fetch();
    }

    /**
     * 返回图片消息给用户
     * @param string $mediaId
     * @return string
     */
    public function genImage($mediaId)
    {
        $this->msgType = 'image';
        $this->data = "<Image>
<MediaId><![CDATA[{$mediaId}]]></MediaId>
</Image>";
        return $this->fetch();
    }

    /**
     * 返回语音消息给用户
     * @param string $mediaId
     * @return string
     */
    public function genVoice($mediaId)
    {
        $this->msgType = 'voice';
        $this->data = "<Voice>
<MediaId><![CDATA[{$mediaId}]]></MediaId>
</Voice>";
        return $this->fetch();
    }

    /**
     * 返回视频给用户
     * @param string $mediaId
     * @param string $title
     * @param string $description
     * @return string
     */
    public function genVideo($mediaId, $title = '', $description = '')
    {
        $this->msgType = 'video';
        $this->data = "<Video>
    <MediaId><![CDATA[$mediaId]]></MediaId>
    <Title><![CDATA[$title]]></Title>
    <Description><![CDATA[$description]]></Description>
  </Video>";
        return $this->fetch();
    }

    /**
     * 返回音乐消息给用户
     * @param string $mediaId
     * @param string $title
     * @param string $description
     * @param string $url
     * @param string $hqUrl
     * @return string
     */
    public function genMusic($mediaId, $title, $description, $url, $hqUrl)
    {
        $this->msgType = 'music';
        $this->data = "<Music>
<Title><![CDATA[$title]]></Title>
<Description><![CDATA[$description]]></Description>
<MusicUrl><![CDATA[$url]]></MusicUrl>
<HQMusicUrl><![CDATA[$hqUrl]]></HQMusicUrl>
<ThumbMediaId><![CDATA[$mediaId]]></ThumbMediaId>
</Music>";
        return $this->fetch();
    }

    /**
     * 返回图文列表给用户
     * @param string $url
     * @param string $picUrl
     * @param string $title
     * @param string $description
     * @return string
     */
    public function genOneNews($url, $picUrl = '', $title = '', $description = '')
    {
        $this->msgType = 'news';
        $this->data = "<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[$title]]></Title> 
<Description><![CDATA[$description]]></Description>
<PicUrl><![CDATA[$picUrl]]></PicUrl>
<Url><![CDATA[$url]]></Url>
</item>
</Articles>";
        return $this->fetch();
    }


    /**
     * 返回图文列表给用户
     * @param array $articles
     * @return string
     */
    public function genNews($articles)
    {
        $this->msgType = 'news';
        $count = count($articles);
        $this->data = "<ArticleCount>{$count}</ArticleCount>
<Articles>
";
        foreach ($articles as $article) {
            $this->data .= "<item>
<Title><![CDATA[{$article['title']}]]></Title> 
<Description><![CDATA[{$article['description']}]]></Description>
<PicUrl><![CDATA[{$article['picurl']}]]></PicUrl>
<Url><![CDATA[{$article['url']}]]></Url>
</item>
";
        }

        $this->data .= "</Articles>";
        return $this->fetch();
    }


    /**
     * @return string
     */
    final private function fetch()
    {
        $content = $this->data ? $this->data : 'success';
        $content = str_replace("{content}", $content, $this->template);
        $content = str_replace("{toUser}", $this->toUser, $content);
        $content = str_replace("{fromUser}", $this->fromUser, $content);
        $content = str_replace("{createTime}", time(), $content);
        $content = str_replace("{msgType}", $this->msgType, $content);
        return $content;
    }
}