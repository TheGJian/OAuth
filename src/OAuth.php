<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:30
 */

namespace zguangjian;

use zguangjian\Contracts\OAuthApplicationInterface;

/**
 * @method method Facebook facebook(array $config)
 * @method method Tencent tencent(array $config)
 * @method method Wechat wechat(array $config)
 * @method method Sina sina(array $config)
 * @method method Google google(array $config)
 */
class OAuth
{
    /**
     * @var Config
     */
    public $config;

    public function __construct($config)
    {
        $this->config = new Config($config);
    }

    /**
     * Magic static call.
     * @param $method
     * @param $param
     * @return mixed
     */
    public static function __callStatic($method, $param)
    {
        $app = new self(...$param);
        return $app->create($method);
    }


    protected function create($method)
    {
        $methodClass = __NAMESPACE__ . '\\OAuth\\' . ucfirst($method);
        if (class_exists($methodClass)) {
            return self::make($methodClass);
        }
        throw new \Exception("Oauth [$method] Not exists");
    }

    /**
     * @param $methodClass
     */

    protected function make($methodClass)
    {
        $app = new $methodClass($this->config);
        if ($app instanceof OAuthApplicationInterface) {
            return $app;
        }
        throw new \Exception("$methodClass Must Be An Instance Of OAuthApplicationInterface");
    }

}