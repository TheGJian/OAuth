<h1 align="center">OAuth For Zguangjian</h1>

## 运行环境

- php >= 7.0
- composer

##  安装
```shell
$ composer require zguangjian/oauth dev-master
```

## 使用方法

### Tencent 腾讯QQ
```php
use zguangjian\OAuth;

/**
 * 跳转第三方平台
* @return mixed
 */
public function Tencent()
{
    $config = [
        'AppKey' => '**********',
        'AppSecret' => '**********',
        'Callback' => 'http://www.guangjian.site/home/tencent',
        'State' => md5(mt_rand()),  
    ];
    return redirect(OAuth::Tencent($config)->getCode());
}
/**
* 回调
* @param $code
* @throws Exception
 */
public function TencentCallBack($code)
{
    $config = [
        'AppKey' => '**********',
        'AppSecret' => '**********',
        'Callback' => 'http://www.guangjian.site/home/tencent',
        'State' => md5(mt_rand()),  
    ];
    $result = OAuth::Tencent($config)->getUserInfo($code);
}
```

### Sina 新浪
```php
use zguangjian\OAuth;

/**
 * 跳转第三方平台
* @return mixed
 */
public function Sina()
{
    $config = [
        'AppKey' => '**********',
        'AppSecret' => '**********',
        'Callback' => 'http://www.guangjian.site/home/sina',
        'State' => md5(mt_rand()),  
    ];
    return redirect(OAuth::Sina($config)->getCode());
}
/**
* 回调地址
* @param $code
* @throws Exception
 */
public function SinaCallBack($code)
{
    $config = [
        'AppKey' => '**********',
        'AppSecret' => '**********',
        'Callback' => 'http://www.guangjian.site/home/sina',
        'State' => md5(mt_rand()),  
    ];
    $result = OAuth::Sina($config)->getUserInfo($code);
}
```
### GitHub
```php
use zguangjian\OAuth;

/**
 * 跳转第三方平台
* @return mixed
 */
public function GitHub()
{
    $config = [
        'AppKey' => '**********',
        'AppName' => 'zguangjian', //应用名称
        'AppSecret' => '**********',
        'Callback' => 'http://www.guangjian.site/home/github',
        'State' => md5(mt_rand()),  
    ];
    return redirect(OAuth::GitHub($config)->getCode());
}
/**
* 回调地址
* @param $code
* @throws Exception
 */
public function GitHubCallBack($code)
{
    $config = [
        'AppKey' => '**********',
        'AppName' => 'zguangjian', //应用名称
        'AppSecret' => '**********',
        'Callback' => 'http://www.guangjian.site/home/github',
        'State' => md5(mt_rand()),  
    ];
    $result = OAuth::GitHub($config)->getUserInfo($code);
}
```
### WindowsLive
```php
use zguangjian\OAuth;

/**
 * 跳转第三方平台
* @return mixed
 */
public function WindowsLive()
{
    $config = [
        'AppKey' => '**********************',
        'AppName' => 'zguangjian',//应用名称
        'AppSecret' => '*******************',
        'Callback' => 'https://www.guangjian.site/home/windowslive',
        'Tenant' => 'consumers',
        'State' => md5(mt_rand()),  
    ];
    return redirect(OAuth::WindowsLive($config)->getCode());
}
/**
* 回调地址
* @param $code
* @throws Exception
 */
public function WindowsLiveCallBack($code)
{
    $config = [
        'AppKey' => '*************************',
        'AppName' => 'zguangjian',//应用名称
        'AppSecret' => '**********************',
        'Callback' => 'https://www.guangjian.site/home/windowslive',
        'Tenant' => 'consumers',
        'State' => md5(mt_rand()),  
       ];
    $result = OAuth::WindowsLive($config)->getUserInfo($code);
}
```