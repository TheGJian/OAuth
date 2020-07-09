<?php

/**
 * Created by PhpStorm.
 * User: zguangjian
 * CreateDate: 2020/5/27 10:25
 * Email: zguangjian@outlook.com
 */

namespace zguangjian\Events;


use zguangjian\Contracts\OAuthApplicationInterface;

abstract class OAuthInterface implements OAuthApplicationInterface
{

    /**
     * @return mixed|void
     */
    public function getCode()
    {
        // TODO: Implement getCode() method.
    }
    
    /**
     * @param string $code
     * @return mixed|void
     */
    public function getUserInfo(string $code)
    {
        // TODO: Implement getUserInfo() method.
    }
}