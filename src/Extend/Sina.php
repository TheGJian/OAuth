<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:31
 */

namespace zguangjian\Extend;

use zguangjian\Config;
use zguangjian\Events\OAuthInterface;
use zguangjian\Http;

/**
 * Class Sina
 * @package zguangjian\Extend
 */
class Sina extends OAuthInterface
{
    protected $payload;
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://api.weibo.com/oauth2/authorize';
    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://api.weibo.com/oauth2/access_token';
    /**
     * 获取用户基本信息api接口
     */
    protected $GetAccessUserInfo = 'https://api.weibo.com/2/users/show.json';

    /**
     * Sina constructor.
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
            'State' => $config->config['State'] ?: md5(mt_rand())
        ];

    }

    /**
     * @return mixed|string|void
     */
    public function getCode()
    {
        $params = [
            'client_id' => $this->payload['AppKey'],
            'redirect_uri' => $this->payload['Callback'],
            'response_type' => $this->payload['ResponseType'],
            'state' => $this->payload['State']
        ];
        return Http::urlSplit($this->GetRequestCodeURL, $params);
    }

    /**
     * @param string $code
     * @return $this|mixed|void
     * @throws \Exception
     */
    protected function getToken(string $code)
    {
        $param = [
            'client_id' => $this->payload['AppKey'],
            'client_secret' => $this->payload['AppSecret'],
            'grant_type' => $this->payload['GrantType'],
            'redirect_uri' => $this->payload['Callback'],
            'code' => $code,
        ];

        $this->payload['accessTokenInfo'] = Http::requestJson(Http::request($this->GetAccessTokenURL, $param, 'POST'));
        $this->payload['openId'] = $this->payload['accessTokenInfo']['uid'];
        $this->payload['accessToken'] = $this->payload['accessTokenInfo']['access_token'];
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
            'access_token' => $this->payload['accessToken'],
            'uid' => $this->payload['openId']
        ];

        $this->payload['userInfo'] = Http::requestJson(Http::request($this->GetAccessUserInfo, $param));
        return $this;

    }
}