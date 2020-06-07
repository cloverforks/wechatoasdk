<?php

namespace Clover\WechatOA;

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
     * @param $content
     * @return mixed|string
     */
    public function generateText($content)
    {
        $this->msgType = 'text';
        $this->data = "<Content><![CDATA[{$content}]]></Content>";
        return $this->fetch();
    }

    /**
     * @param $mediaId
     * @return mixed|string
     */
    public function generateImage($mediaId)
    {
        $this->msgType = 'image';
        $this->data = "<Image>
<MediaId><![CDATA[{$mediaId}]]></MediaId>
</Image>";
        return $this->fetch();
    }

    /**
     * @param $mediaId
     * @return $this
     */
    public function generateVoice($mediaId)
    {
        $this->msgType = 'voice';
        $this->data = "<Voice>
<MediaId><![CDATA[{$mediaId}]]></MediaId>
</Voice>";
        return $this;
    }

    /**
     * @param $mediaId
     * @param $title
     * @param $description
     * @return mixed|string
     */
    public function generateVideo($mediaId, $title, $description)
    {
        $this->msgType = 'video';
        $this->data = "<Video>
<MediaId><![CDATA[{$mediaId}]]></MediaId>
<Title><![CDATA[{$title}]]></Title>
<Description><![CDATA[{$description}]]></Description>
</Video>";
        return $this->fetch();
    }

    /**
     * @param $mediaId
     * @param $title
     * @param $description
     * @param $url
     * @param $hqUrl
     * @return mixed|string
     */
    public function generateMusic($mediaId, $title, $description, $url, $hqUrl)
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
     * @param $articles
     * @return mixed|string
     */
    public function generateNews($articles)
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
     * @return mixed|string
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