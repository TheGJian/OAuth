<?php

/**
 * Created by PhpStorm.
 * User: zguangjian
 * CreateDate: 2020/5/27 19:40
 * Email: zguangjian@outlook.com
 */

namespace zguangjian\Extend;

use zguangjian\Config;
use zguangjian\Events\OAuthInterface;
use zguangjian\Http;

class GitHub extends OAuthInterface
{
    public $payload;

    protected $GetRequestCodeURL = "https://github.com/login/oauth/authorize";

    protected $GetRequestTokenURL = "https://github.com/login/oauth/access_token";

    protected $GetAccessUserInfo = "https://api.github.com/user";

    public function __construct(Config $config)
    {
        $this->payload = [
            'AppKey' => $config->config['AppKey'],
            'AppSecret' => $config->config['AppSecret'],
            'Callback' => $config->config['Callback'],
            'Version' => $config->Version,
            'ResponseType' => $config->ResponseType,
            'GrantType' => $config->GrantType,
            'state' => md5(mt_rand(1, 100))
        ];
    }

    /**
     * @return mixed|string|void
     */
    public function getCode()
    {
        $param = [
            'client_id' => $this->payload['AppKey'],
            'redirect_uri' => $this->payload['Callback'],
            'scope' => 'user',
            'state' => $this->payload['state']
        ];

        return Http::urlSplit($this->GetRequestCodeURL, $param);
    }

    /**
     * @param string $code
     * @return $this|mixed|void
     * @throws \Exception
     */
    public function getToken(string $code)
    {
        $param = [
            'client_id' => $this->payload['AppKey'],
            'client_secret' => $this->payload['AppSecret'],
            'code' => $code,
            'redirect_uri' => $this->payload['Callback'],
            'state' => $this->payload['state'],
        ];
        $res = Http::request($this->GetRequestTokenURL, $param, 'POST', ['Accept: application/json']);
        $res = json_decode($res);
        $this->payload['accessToken'] = $res->access_token;
        return $this;
    }

    /**
     * @param string $code
     * @return $this|mixed|void
     * @throws \Exception
     */
    public function getUserInfo(string $code)
    {
        $this->getToken($code);
        $param = [
            'access_token' => $this->payload['accessToken']
        ];
        $res = Http::request($this->GetAccessUserInfo, $param);
        $res = json_decode($res);
        $this->payload['userInfo'] = $res;
        return $this;

    }
}