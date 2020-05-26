<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:30
 */

namespace zguangjian;

use zguangjian\Contracts\OAuthApplicationInterface;


abstract class Tencent implements OAuthApplicationInterface
{
    protected $config;
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
        $this->config = Config::class;
    }

    public function getCode()
    {
        $params = [
            'client_id' => $this->AppKey,
            'redirect_uri' => $this->Callback,
            'response_type' => $this->ResponseType,
            'state' => md5(rand(1, 100))
        ];
        return Http::urlSplit($this->GetRequestCodeURL, $params);
    }

    public function getToken(string $code)
    {
        $params = [
            'client_id' => $this->AppKey,
            'client_secret' => $this->AppSecret,
            'grant_type' => $this->GrantType,
            'code' => $code,
            'redirect_uri' => $this->Callback,
        ];
        $res = Http::request($this->GetAccessTokenURL, $params);
        parse_str($res, $data);
        $this->config->openid = $this->getOpenId($data['access_token']);
        return $this;
    }

    public function getUserInfo(string $openId, string $token)
    {
        $param = [
            'access_token' => $token,
            'oauth_consumer_key' => $this->AppKey,
            'openid' => $openId
        ];
        $res = Http::request($this->GetAccessUserInfo, $param);
        return $res;
    }

    protected function getOpenId($token)
    {
        $params = [
            'access_token' => $this->Token,
            'oauth_consumer_key' => $this->AppKey,
            'openid' => $this->OpenId
        ];
        $res = Http::request($this->GetAccessOpenId, $params);
        $data = json_decode(substr(substr($res, strpos($res, '{')), 0, strpos(substr($res, strpos($res, '{')), '}') + 1));
        return $data->openid;
    }

    public function getUserInfoByCode($code)
    {
        if (empty($code)) throw new \Exception('请传递code值');
        $params = [
            'client_id' => $this->AppKey,
            'client_secret' => $this->AppSecret,
            'grant_type' => $this->GrantType,
            'code' => $code,
            'redirect_uri' => $this->Callback,
        ];
        $res = $this->getAccessToken($params);
        parse_str($res, $data);
        $this->Token = $data['access_token'];
        $this->Other = $data;
        $params = [
            'access_token' => $this->Token,
            'oauth_consumer_key' => $this->AppKey,
            'openid' => $this->OpenId
        ];
        $res = $this->getUserInfo($params);
        if ($res->ret == 0) {
            $this->UserInfo = $res;
        } else {
            throw new \Exception($res->msg);
        }
        return $this;
    }
}