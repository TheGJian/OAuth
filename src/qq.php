<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:30
 */

namespace zguangjian;

use zguangjian\OAuth;

class qq extends OAuth
{
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
		$res = $this->getRequestCodeUrl($params);
		return $res;
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