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

3. 用如下方法使用Direct类
```
<?php

namespace App\Http\Controllers;

use Lanqiukun\Alioss\Direct;

class TestCtrl extends Controller
{

    static public function test()
    {
        $callback_url = env('APP_URL') . '/api/test_callback';
        $dir = 'qwer/asdf';
        $max_body_size = 2000;

        return Direct::sign_policy($callback_url, $dir, $max_body_size);

    }

    static public function test_callback()
    {
        $fileinfo = request()->all();
        $public_key = file_get_contents(base64_decode($_SERVER['HTTP_X_OSS_PUB_KEY_URL']));
        $body = file_get_contents('php://input');
        $path = $_SERVER['REQUEST_URI'];

        return Direct::callback($fileinfo, $public_key, $body, $path);
    }
}

```