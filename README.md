<h1 align="center">OAuth</h1>

欢迎 Star，欢迎 PR！

## 特点
- 命名不那么乱七八糟
- 隐藏开发者不需要关注的细节
 


## 运行环境
- PHP 5.6+
- composer


## 支持的参数
       初始化统一参数 
       $config = [
            'app_id' => '' ,
            'app_secret' => '',
            'callback' => ''
       ]
### 1、qq登录
      public function login()
      {
            $OAuth = new qq($config);
            echo $OAuth->getCodeUrl();
      }
       public function callback()
      {
            $OAuth = new qq($config);
            $code = Input::get('code');
            $obj = $OAuth->getUserInfoByCode($code); //返回对象或错误结果
       }
      
### 2、微信登录
     public function login()
      {
            $OAuth = new wx($config);
            echo $OAuth->getCodeUrl();
      }
       public function callback()
      {
            $OAuth = new wx($config);
            $code = Input::get('code');
            $obj = $OAuth->getUserInfoByCode($code); //返回对象或错误结果
       }
### 3、微博登录
     public function login()
      {
            $OAuth = new sina($config);
            echo $OAuth->getCodeUrl();
      }
       public function callback()
      {
            $OAuth = new sina($config);
            $code = Input::get('code');
            $obj = $OAuth->getUserInfoByCode($code); //返回对象或错误结果
       }
## 代码贡献
由于测试及使用环境的限制，本项目中只开发了qq,微信,新浪微博的相关登录。


## LICENSE
MIT