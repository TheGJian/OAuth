<?php

/**
 * Created by PhpStorm.
 * User: zguangjian
 * CreateDate: 2020/5/28 16:40
 * Email: zguangjian@outlook.com
 */

namespace zguangjian\Extend;


use zguangjian\Config;
use zguangjian\Events\OAuthInterface;
use zguangjian\Http;

class WindowsLive extends OAuthInterface
{
    public $payload;

    /**
     * @var string
     */
    protected $GetRequestCodeURL = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize";
    /**
     * @var string
     */
    protected $GetAccessTokenURL = "https://login.microsoftonline.com/common/oauth2/v2.0/token";
    /**
     * @var string
     */
    protected $GetAccessUserInfo = "https://graph.microsoft.com/v1.0/me";

    /**
     * WindowsLive constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->payload = [
            'AppKey' => $config->config['AppKey'],
            'AppSecret' => $config->config['AppSecret'],
            'Callback' => $config->config['Callback'],
            'Version' => $config->Version,
            'ResponseType' => $config->ResponseType,
            'GrantType' => $config->GrantType,
            'state' => md5(mt_rand(1, 100)),
            'scope' => 'openid profile offline_access user.read calendars.read'
        ];
    }

    /**
     * @return mixed|string|void
     */
    public function getCode()
    {
        $param = [
            "client_id" => $this->payload['AppKey'],
            "redirect_uri" => $this->payload['Callback'],
            "scope" => $this->payload['scope'],
            "state" => $this->payload['state'],
            "response_type" => $this->payload['ResponseType'],
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
            'redirect_uri' => $this->payload['Callback'],
            'grant_type' => $this->payload['GrantType'],
            'scope' => $this->payload['scope'],
            'code' => $code
        ];

        $this->payload['accessTokenInfo'] = Http::requestJson(Http::request($this->GetAccessTokenURL, $param, 'POST', ["Content-Type: application/x-www-form-urlencoded"]));
        $this->payload['accessToken'] = $this->payload['accessTokenInfo']->access_token;
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
            "Authorization: Bearer " . $this->payload['accessToken'],
            "Host: graph.microsoft.com"
        ];
        $this->payload['userInfo'] = Http::requestJson(Http::request($this->GetAccessUserInfo, [], 'GET', $head));
        $this->payload['openId'] = $this->payload['id'];
        return $this;

    }
}