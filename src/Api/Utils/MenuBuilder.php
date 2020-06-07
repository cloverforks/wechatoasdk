<?php


namespace App;


/**
 * Class MenuBuilder
 * @package App
 */
class MenuBuilder {

    private $data = [];
    private $matchRule = '';

    public function addClick($name, $key) {
        array_push($this->data, ["type" => "click", "name" => $name, "key" => $key]);
        return $this;
    }

    public function addView($name, $url) {
        array_push($this->data, ["type" => "view", "name" => $name, "url" => $url]);
        return $this;
    }

    public function addQRCodePush($event, $key) {
        array_push($this->data, ["type" => "scancode_push", "name" => $event, "key" => $key]);
        return $this;
    }

    public function addQRCodeWaitMsg($tip, $key) {
        array_push($this->data, ["type" => "scancode_waitmsg", "name" => $tip, "key" => $key]);
        return $this;
    }

    public function addPhoto($tip, $key) {
        array_push($this->data, ["type" => "pic_sysphoto", "name" => $tip, "key" => $key]);
        return $this;
    }

    public function addAlbumImage($tip, $key) {
        array_push($this->data, ["type" => "pic_photo_or_album", "name" => $tip, "key" => $key]);
        return $this;
    }

    public function addWeixinImage($tip, $key) {
        array_push($this->data, ["type" => "pic_weixin", "name" => $tip, "key" => $key]);
        return $this;
    }

    public function addLocationRequest($tip, $key) {
        array_push($this->data, ["type" => "location_select", "name" => $tip, "key" => $key]);
        return $this;
    }

    public function addMedia($tip, $mediaId) {
        array_push($this->data, ["type" => "media_id", "name" => $tip, "media_id" => $mediaId]);
        return $this;
    }

    public function addLimitedView($tip, $mediaId) {
        array_push($this->data, ["type" => "view_limited", "name" => $tip, "media_id" => $mediaId]);
        return $this;
    }

    public function addSubMenu($name, $menu) {
        array_push($this->data, ["name" => "$name", "sub_button" => $menu->getList()]);
    }

    public function getList() {
        return $this->data;
    }

    /**
     *
     * @param string $tagid
     * @param string $sex
     * @param string $client_platform_type
     * @param string $language
     * @param string $country
     * @param string $province
     * @param string $city
     * @return array
     */
    public function addMatchRule($tagid = '', $sex = '', $client_platform_type = '', $language = '', $country = '', $province = '', $city = '') {
        $data = [];
        if ($tagid) {
            $data['tag_id'] = $tagid;
        }
        if ($sex) {
            $data['sex'] = $sex;
        }
        if ($client_platform_type) {
            $data['client_platform_type'] = $client_platform_type;
        }
        if ($language) {
            $data['language'] = $language;
        }
        if ($country && $province && $city) {
            $data['country'] = $country;
            $data['province'] = $province;
            $data['city'] = $city;
        }
        return $data;
    }

    public function pack() {
        $temp = ["button" => $this->data];
        if (!empty($this->matchRule)) {
            $temp['matchrule'] = $this->matchRule;
        }
        return json_encode($temp);
    }

}