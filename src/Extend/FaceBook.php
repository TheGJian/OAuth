<?php
/**
 * Created by PhpStorm.
 * User: zguangjian
 * Date: 2019/7/24
 * Time: 11:28
 * Email: zguangjian@outlook.com
 */

namespace zguangjian\Extend;

use zguangjian\Config;
use zguangjian\Events\OAuthInterface;
use zguangjian\Http;

/**
 * Class FaceBook
 * @package zguangjian\Extend
 */
class FaceBook extends OAuthInterface
{
    public $payload;
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://www.facebook.com/v7.0/dialog/oauth';
    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://graph.facebook.com/v7.0/oauth/access_token';
    /**
     * 获取用户基本信息api接口
     */
    protected $GetAccessUserInfo = 'https://graph.facebook.com/v7.0/me';

    /**
     * FaceBook constructor.
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
            'State' => $config->config['State'] ?: md5(mt_rand()),
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
     * @return mixed|void
     * @throws \Exception
     */
    public function getToken(string $code)
    {
        $params = [
            'client_id' => $this->payload['AppKey'],
            'client_secret' => $this->payload['AppSecret'],
            'code' => $code,
            'redirect_uri' => $this->payload['Callback'],
        ];
        $this->payload['AccessTokenInfo'] = Http::requestJson(Http::requestJson($this->GetAccessTokenURL, $params));
        $this->payload['AccessToken'] = $this->payload['AccessTokenInfo']['access_token'];
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
            'fields' => 'id,name,about,address,birthday,favorite_athletes'
        ];
        $this->payload['userInfo'] = Http::requestJson(Http::request($this->GetAccessUserInfo, $param));
        $this->payload['openId'] = $this->payload['userInfo']['id'];
        return $this;
    }


}