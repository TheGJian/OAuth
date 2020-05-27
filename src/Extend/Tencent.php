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
use zguangjian\OAuth;

/**
 * Class Tencent
 * @package zguangjian\Extend
 * @method getCode code  跳转第三方登陆
 */
class Tencent extends OAuthInterface
{
    public $payload;
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://graph.qq.com/oauth2.0/authorize';
    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://graph.qq.com/oauth2.0/token';
    /**
     * 获取openId 的api接口
     * @var string
     */
    protected $GetAccessOpenId = 'https://graph.qq.com/oauth2.0/me';
    /**
     * 获取用户基本信息api接口
     */
    protected $GetAccessUserInfo = 'https://graph.qq.com/user/get_user_info';
    /**
     * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = 'scope=get_user_info,add_share';
    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://graph.qq.com/';

    public function __construct(Config $config)
    {
        $this->payload = [
            'AppKey' => $config->config['AppKey'],
            'AppSecret' => $config->config['AppSecret'],
            'Callback' => $config->config['Callback'],
            'Version' => $config->Version,
            'ResponseType' => $config->ResponseType,
            'GrantType' => $config->GrantType,
        ];
    }

    /**
     * 跳转到第三方登陆平台
     * @return mixed|string|void
     */
    public function getCode()
    {
        $params = [
            'client_id' => $this->payload['AppKey'],
            'redirect_uri' => $this->payload['Callback'],
            'response_type' => $this->payload['ResponseType'],
            'state' => md5(rand(1, 100))
        ];
        return Http::urlSplit($this->GetRequestCodeURL, $params);
    }


    public function getToken(string $code)
    {
        $params = [
            'client_id' => $this->payload['AppKey'],
            'client_secret' => $this->payload['AppSecret'],
            'grant_type' => $this->payload['GrantType'],
            'code' => $code,
            'redirect_uri' => $this->payload['Callback'],
        ];
        $res = Http::request($this->GetAccessTokenURL, $params);
        parse_str($res, $data);
        $this->payload['accessToken'] = $data['access_token'];
        return $this;
    }

    public function getUserInfo(string $code)
    {
        $this->getToken($code)->getOpenId();
        $param = [
            'access_token' => $this->payload['accessToken'],
            'oauth_consumer_key' => $this->payload['AppKey'],
            'openid' => $this->payload['openId']
        ];

        $res = Http::request($this->GetAccessUserInfo, $param);
        $res = json_decode($res, true);
        $this->payload['userInfo'] = $res;
        return $this->payload;
    }

    public function getOpenId()
    {
        $params = [
            'access_token' => $this->payload['accessToken'],
        ];
        $res = Http::request($this->GetAccessOpenId, $params);

        $data = json_decode(substr(substr($res, strpos($res, '{')), 0, strpos(substr($res, strpos($res, '{')), '}') + 1));
        $this->payload['openId'] = $data->openid;
        
        return $this;
    }


}