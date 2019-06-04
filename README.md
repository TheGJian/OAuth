<h1 align="center">OAuth</h1>

欢迎 Star，欢迎 PR！

## 特点
- 命名不那么乱七八糟
- 隐藏开发者不需要关注的细节
- 根据支付宝、微信最新 API 开发而成
- 高度抽象的类，免去各种拼json与xml的痛苦
- 符合 PSR 标准，你可以各种方便的与你的框架集成
- 文件结构清晰易理解，可以随心所欲添加本项目中没有的支付网关
- 方法使用更优雅，不必再去研究那些奇怪的的方法名或者类名是做啥用的


## 运行环境
- PHP 5.6+
- composer


## 支持的支付方法
### 1、qq登录
      public function login()
      {
            $OAuth = new qq();
            echo $OAuth->getCodeUrl();
      }
       public function callback()
      {
            $code = Input::get('code');
            $obj = $OAuth->getUserInfoByCode($code); //返回对象或错误结果
       }
      
### 2、微信登录
     public function login()
      {
            $OAuth = new wx();
            echo $OAuth->getCodeUrl();
      }
       public function callback()
      {
            $OAuth = new wx();
            $code = Input::get('code');
            $obj = $OAuth->getUserInfoByCode($code); //返回对象或错误结果
       }
### 3、微博登录
     public function login()
      {
            $OAuth = new sina();
            echo $OAuth->getCodeUrl();
      }
       public function callback()
      {
            $OAuth = new sina();
            $code = Input::get('code');
            $obj = $OAuth->getUserInfoByCode($code); //返回对象或错误结果
       }
## 代码贡献
由于测试及使用环境的限制，本项目中只开发了qq,微信,新浪微博的相关登录。


## LICENSE
MIT