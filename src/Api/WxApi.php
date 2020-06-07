<?php


namespace Clover\WechatOA\Api;


/**
 * Class WxApi
 */
class WxApi
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var int
     */
    private $accessExpired;

    /**
     * @var array
     */
    private $components = [];

    /**
     * WxApi constructor.
     * @param string $accessToken
     * @param int $accessExpired
     */
    final private function __construct($accessToken, $accessExpired)
    {
        $this->accessToken = $accessToken;
        $this->accessExpired = $accessExpired;
        $this->components[self::class] = $this;
    }

    final public function __get($name)
    {
        $className = 'Clover\\WechatOA\\Api\\' . ucfirst($name);
        if (time() - $this->accessToken < 300)
            $this->renewToken();

        if (!isset($this->components[$className]))
            $this->components[$className] = new $className($this->accessToken);
        return $this->components[$className];
    }

    /**
     * @todo 继承并实现此方法实现token刷新
     */
    public function renewToken()
    {

    }
}