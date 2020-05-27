<?php
/**
 * Created by PhpStorm.
 * User: zguangjian
 * Date: 2020/3/23
 * Time: 15:25
 * Email: zguangjian@outlook.com
 */

namespace zguangjian;


class Config
{
    /**
     * @var array|Config
     */
    public $config = [];
    /**
     * oauth版本
     * @var string
     */
    public $Version = '2.0';
    /**
     * 授权类型 response_type 目前只能为code
     * @var string
     */
    public $ResponseType = 'code';
    /**
     * grant_type 目前只能为 authorization_code
     * @var string
     */
    public $GrantType = 'authorization_code';


    public function __construct($config)
    {
        $this->config = $config;
    }


    public function __get($key)
    {
        return $this->getConfig($key);
    }

    public function getConfig($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->config->all();
        }
        if ($this->config->has($key)) {
            return $this->config[$key];
        }
        return $default;
    }
}