<?php
/**
 * Created by PhpStorm.
 * User: zguangjian
 * Date: 2020/3/25
 * Time: 10:50
 * Email: zguangjian@outlook.com
 */

namespace zguangjian\Contracts;


interface OAuthApplicationInterface
{
    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @param string $code
     * @return mixed
     */
    public function getToken(string $code);

    /**
     * @param string $openId
     * @param string $token
     * @return mixed
     */
    public function getUserInfo(string $openId, string $token);
}