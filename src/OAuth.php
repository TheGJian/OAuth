<?php
/**
 * Created by PhpStorm.
 * User: Tjian
 * Date: 2019/5/9
 * Time: 21:30
 */

namespace zguangjian\oauth;

class OAuth
{

    /**
     * oauth版本
     * @var string
     */
    protected $Version = '2.0';
    /**
     * 申请应用时分配的app_key
     * @var string
     */
    protected $AppKey = '';
    /**
     * 申请应用时分配的 app_secret
     * @var string
     */
    protected $AppSecret;
    /**
     * 授权类型 response_type 目前只能为code
     * @var string
     */

    protected $ResponseType = 'code';
    /**
     * grant_type 目前只能为 authorization_code
     * @var string
     */
    protected $GrantType = 'authorization_code';
    /**
     * 回调页面URL  可以通过配置文件配置
     * @var string
     */
    protected $Callback = '';
    /**
     * 获取request_code的额外参数 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = '';
    /**
     * 获取request_code请求的URL
     * @var string
     */
    protected $GetRequestCodeURL = '';
    /**
     * 获取access_token请求的URL
     * @var string
     */
    protected $GetAccessTokenURL = '';
    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = '';
    /**
     * 授权后获取到的TOKEN信息
     * @var array
     */
    protected $Token = null;
    /**
     * 调用接口类型
     * @var string
     */
    private $Type = '';
    /**
     * 获取的用户id
     * @var string
     */
    public $OpenId;

    /**
     * 保存用户access_token refresh_token参数
     */
    public $Other;
    /**
     * 用户基本信息
     */
    public $UserInfo;

    public  function __construct($Token)
    {
        $class = get_class($this);
        $this->Type = strtoupper(substr(strrchr($class,"\\"),1));
        if (!empty(env($this->Type.'_KEY')) || !empty(env($this->Type,'_SECRET')) || !empty(env($this->Type.'_CALLBACK'))){
            $this->AppKey = env($this->Type.'_KEY');
            $this->AppSecret = env($this->Type.'_SECRET');
            $this->Callback = env($this->Type.'_CALLBACK');

        }else{
            throw new \Exception('请先配置参数');
        }
    }

    /**
     * 跳转登录地址
     * @param $params
     */
    public function getRequestCodeUrl($params)
    {
        return  "<script language='javascript' type='text/javascript'>window.location.href='".$this->GetRequestCodeURL.'?'.http_build_query($params)  ."'</script>";
    }

    public function getAccessToken($params,$method = 'GET')
    {
        return $this->http($this->GetAccessTokenURL,$params,$method);
    }

    public function getOpenId()
    {

        if (empty($this->OpenId)){
            $data = $this->http($this->GetAccessOpenId,['access_token'=>$this->Token]);

            $data = json_decode(trim(substr($data, 9), " );\n"), true);

            if (isset($data['openid'])){
                $this->OpenId = $data['openid'];
            } else{
                throw new \Exception('没有获取到openid！');
            }
        }

        return $this->OpenId;
    }

    public function getUserInfo($param)
    {
        if (empty($this->OpenId)){
            $this->getOpenId();
            $param['openid'] = $this->OpenId;
        }

        return json_decode($this->http($this->GetAccessUserInfo,$param),false) ;
    }

    /**
     * @param $url 请求url
     * @param $params 携带的参数
     * @param string $method 请求类型
     * @param array $header 请求头部
     * @param bool $multi
     */
    protected function http($url , $params = [] , $method = 'GET',$header = [],$multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }

    /**
     * @param $api api名称
     * @param string $fix 后缀
     */
    protected function url($api ,$fix = '')
    {
        return $this->http($this->ApiBase . $api . $fix);
    }
}