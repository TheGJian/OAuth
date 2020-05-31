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
            'AppName' => $config->config['AppName'],
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

        $this->payload['accessTokenInfo'] = Http::requestJson(Http::request($this->GetRequestTokenURL, $param, 'POST', ['Accept: application/json']));
        $this->payload['accessToken'] = $this->payload['accessToken']['access_token'];
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
        $head = [
            'Authorization:token ' . $this->payload['accessToken'],
            'User-Agent:' . $this->payload['AppName']
        ];

        $this->payload['userInfo'] = Http::requestJson(Http::request($this->GetAccessUserInfo, [], 'GET', $head));
        return $this;

    }
}