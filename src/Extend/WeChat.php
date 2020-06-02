<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:30
 */

namespace zguangjian\Extend;

use zguangjian\Config;
use zguangjian\Events\OAuthInterface;
use zguangjian\Http;

/**
 * Class WeChat
 * @package zguangjian\Extend
 */
class WeChat extends OAuthInterface
{
    /**
     * @var array
     */
    public $payload;
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    /**
     * 获取用户基本信息api接口
     */
    protected $GetAccessUserInfo = 'https://api.weixin.qq.com/sns/userinfo';

    /**
     * WeChat constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->payload = [
            $this->payload = [
                'AppKey' => $config->config['AppKey'],
                'AppSecret' => $config->config['AppSecret'],
                'Callback' => $config->config['Callback'],
                'Version' => $config->Version,
                'ResponseType' => $config->ResponseType,
                'GrantType' => $config->GrantType,
                'State' => $config->config['State'] ?: md5(mt_rand()),
                'Scope' => $config->config['State'] ?: md5(mt_rand()),
            ]
        ];
    }

    /**
     * @return mixed|string|void
     */
    public function getCode()
    {
        $params = [
            'appid' => $this->payload['AppKey'],
            'redirect_uri' => $this->payload['Callback'],
            'response_type' => $this->payload['ResponseType'],
            'state' => $this->payload['State'],
            'scope' => $this->payload['Scope']
        ];
        return Http::urlSplit($this->GetRequestCodeURL, $params) . '#wechat_redirect';
    }

    /**
     * @param string $code
     * @return $this|mixed|void
     * @throws \Exception
     */
    public function getToken(string $code)
    {
        $param = [
            'appid' => $this->payload['AppKey'],
            'secret' => $this->payload['AppSecret'],
            'grant_type' => $this->payload['GrantType'],
            'code' => $code,
        ];
        $this->payload['AccessTokenInfo'] = Http::requestJson(Http::request($this->GetAccessTokenURL, $param));
        $this->payload['AccessToken'] = $this->payload['AccessTokenInfo']['access_token'];
        $this->payload['openId'] = $this->payload['AccessTokenInfo']['openid'];
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
            'access_token' => $this->payload['AccessToken'],
            'openid' => $this->payload['openId'],
            'lang' => 'zh_CN'
        ];
        $this->payload['userInfo'] = Http::requestJson(Http::request($this->GetAccessUserInfo, $param));
        return $this;
    }

}