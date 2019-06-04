<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:31
 */

namespace zguangjian\OAuth\src;


use zguangjian\OAuth\OAuth;

class sina extends OAuth
{
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

    public function __construct($Token = null)
    {
        parent::__construct($Token);
    }

    public function getCodeUrl()
    {
        $params = [
            'client_id' => $this->AppKey,
            'redirect_uri' => $this->Callback,
            'response_type' =>$this->ResponseType,
            'state' => md5(url('home/public/qqCallback'))
        ];

        return $this->GetRequestCodeURL($params);
    }

    public function getUserInfoByCode($code)
    {
        $params = [
            'client_id' => $this->AppKey,
            'client_secret' => $this->AppSecret,
            'grant_type' => $this->GrantType,
            'code' => $code,
            'redirect_uri'=> $this->Callback
        ];

        $res = $this->getAccessToken($params,'POST');

        $data = json_decode($res,true);

        $this->Token = $data['access_token'];
        $this->OpenId = $data['uid'];
        $this->Other = $data;

        $params = [
            'access_token' => $this->Token,
            'uid' => $this->OpenId
        ];

        $res = $this->getUserInfo($params);
        if (empty($res->error_code)){

            $this->UserInfo = $res;
        } else {
            throw new \Exception($res->error);
        }

        return $this;
    }


}