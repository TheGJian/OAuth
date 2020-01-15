<?php
/**
 * Created by PhpStorm.
 * User: zguangjian
 * Date: 2019/7/24
 * Time: 11:28
 * Email: zguangjian@outlook.com
 */

namespace zguangjian;

class facebook extends OAuth
{
	/**
	 * 获取requestCode的api接口
	 * @var string
	 */
	protected $GetRequestCodeURL = 'https://www.facebook.com/v3.3/dialog/oauth';
	/**
	 * 获取access_token的api接口
	 * @var string
	 */
	protected $GetAccessTokenURL = 'https://graph.facebook.com/v3.3/oauth/access_token';
	/**
	 * 获取用户基本信息api接口
	 */
	protected $GetAccessUserInfo = 'https://graph.facebook.com/me';

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
		return $this->GetRequestCodeURL($params);
	}

	public function getUserInfoByCode($code)
	{
		$params = [
			'client_id' => $this->AppKey,
			'client_secret' => $this->AppSecret,
			'grant_type' => $this->GrantType,
			'code' => $code,
			'redirect_uri' => $this->Callback,
		];
		$res = $this->getAccessToken($params, 'GET');
		$data = json_decode($res, true);
		$this->Token = $data['access-token'];
		$this->Other = $data;
		return $this;
	}
}