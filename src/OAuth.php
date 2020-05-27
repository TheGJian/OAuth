<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:30
 */

namespace zguangjian;

use zguangjian\Contracts\OAuthApplicationInterface;
use zguangjian\Extend\FaceBook;
use zguangjian\Extend\Google;
use zguangjian\Extend\Sina;
use zguangjian\Extend\Tencent;
use zguangjian\Extend\WeChat;

/**
 * Class OAuth
 * @package zguangjian
 * @method static FaceBook  FaceBook(array $config)     FaceBook登陆
 * @method static Google    Google(array $config)       谷歌登陆
 * @method static Sina      Sina(array $config)         新浪登陆
 * @method static Tencent   Tencent(array $config)      腾讯QQ登陆
 * @method static WeChat    WeChat(array $config)       微信登陆
 */
class OAuth
{
    /**
     * @var Config
     */
    public $config;

    /**
     * OAuth constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = new Config($config);
    }

    /**
     * @param $method
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $param)
    {

        $app = new self(...$param);
        return $app->create($method);
    }

    /**
     * @param $method
     * @return mixed
     * @throws \Exception
     */
    protected function create($method)
    {
        $methodClass = __NAMESPACE__ . '\\Extend\\' . ucfirst($method);

        if (class_exists($methodClass)) {
            return self::make($methodClass);
        }
        throw new \Exception("Extends [$method] Not exists");
    }

    /**
     * @param $methodClass
     * @return mixed
     * @throws \Exception
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