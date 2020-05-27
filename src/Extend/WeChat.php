<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:30
 */

namespace zguangjian\Extend;

use zguangjian\Events\OAuthInterface;

class WeChat extends OAuthInterface
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    /**
     * 获取用户基本信息api接口
     */
    protected $GetAccessUserInfo = 'https://api.weixin.qq.com/sns/userinfo';


    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function getCodeUrl()
    {
        $params = [
            'client_id' => $this->AppKey,
            'redirect_uri' => $this->Callback,
            'response_type' => $this->ResponseType,
            'state' => md5(rand(1, 100))
        ];
        return $this->getRequestCodeUrl($params);
    }

    public function getUserInfoByCode($code)
    {
        if (empty($code)) throw new \Exception('请传递code值');
        $params = [
            'appid' => $this->AppKey,
            'secret' => $this->AppSecret,
            'grant_type' => $this->GrantType,
            'code' => $code,
        ];
        $res = $this->getAccessToken($params);
        $data = json_decode($res, true);
        $this->Token = $data['access_token'];
        $this->OpenId = $data['openid'];
        $this->Other = $data;
        $params = [
            'access_token' => $this->Token,
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