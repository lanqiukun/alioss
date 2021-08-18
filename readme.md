### 说明
这是一个阿里云的oss服务端签名直传的laravel sdk

### 先决条件

1. 必须用composer 2.0或以上的版本进行安装

### 使用方法

1. 安装拓展
```
composer require lanqiukun/alioss
```

2. 在laravel的 *.env* 文件中配置好阿里云oss的参数，例如
```
ALIOSS_ACCESS_KEY_ID=YOUR_ALIOSS_ACCESS_KEY_ID
ALIOSS_ACCESS_KEY_SECRET=YOUR_ALIOSS_ACCESS_KEY_SECRET
ALIOSS_BUCKET=https://yourbucketname.region.aliyuncs.com
```

3. 业务逻辑中用如下方法使用Direct类
```
<?php

namespace App\Http\Controllers;

use Lanqiukun\Alioss\Direct;

class TestCtrl extends Controller
{

    static public function test()
    {
        //业务条件检查，例如权限是否满足，是否达到最大文件上传数量限制等。。。

        $callback_url = env('APP_URL') . '/api/test_callback';  //设置oss上传成功的回调，回调返回的内容将原封不动地返回客户端
        $dir = 'qwer/asdf';         //限制客户端只能将文件上传到oss的qwer/asdf目录下
        $max_body_size = 2000;      //限制客户端最大上传文件大小2000KB

        return Direct::sign_policy($callback_url, $dir, $max_body_size);

    }

    static public function test_callback()
    {
        //客户端上传的文件的信息
        $fileinfo = request()->all();

        //根据以下请求参数验证签名
        $public_key = file_get_contents(base64_decode($_SERVER['HTTP_X_OSS_PUB_KEY_URL']));
        $body = file_get_contents('php://input');
        $path = $_SERVER['REQUEST_URI'];


        //阿里云oss服务器会将此处返回的信息原封不动地返回给上传文件的客户端
        return Direct::callback($fileinfo, $public_key, $body, $path);
    }
}
```

4. 客户端将sign_policy函数返回的参数 + 想要上传的文件 用 formdata的形式post到阿里云oss服务器，见postman示例：
[postman - demo]()

文档链接：

阿里云oss：https://help.aliyun.com/product/31815.html


