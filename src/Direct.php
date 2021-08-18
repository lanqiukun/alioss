<?php

namespace Lanqiukun\Alioss;

class Direct 
{

    //callback_url  示例值：'https://oss.gign.xyz/api/oss_callback  //客户端将文件直接post到阿里云服务器后触发的回调url
    //dir           示例值：'myservice/feedback/'                   //限制客户端上传文件的文件路径，没有相应的文件目录则创建
    //max_body_size 示例值：1024 * 20  单位KB                       //上传文件最大字节数 20MB
    //expire        示例值：120        单位秒                       //policy在签发后多少秒内有效，在有效期内的policy可以重复使用
    //min_body_size 示例值：0          单位KB                       //上传文件最小字节数
    static public function sign_policy($callback_url, $dir, $max_body_size, $expire = 300, $min_body_size = 0)
    {

        $id = env('ALIOSS_ACCESS_KEY_ID');          // 请填写您的AccessKeyId。
        $key = env('ALIOSS_ACCESS_KEY_SECRET');     // 请填写您的AccessKeySecret。
        $host = env('ALIOSS_BUCKET');               // Bucket 域名
        $callback_param = [
            'callbackUrl' => $callback_url,         
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        ];

        function gmt_iso8601($time)
        {
            $mydatetime = new \DateTime(date("c", $time));
            $expiration = $mydatetime->format(\DateTime::ISO8601);
            return substr($expiration, 0, strpos($expiration, '+')) . "Z";
        }

        $end = time() + $expire;
        $expiration = gmt_iso8601($end);


        //客户端上传文件的限制
        //content-length-range  文件大小方面的限制
        //starts-with           oss的上传文件路径限制（必须由服务端控制客户端上传的文件路径）
        $conditions = [
            ['content-length-range', $min_body_size, $max_body_size * 1024],
            ['starts-with', '$key', $dir],
        ];


        $policy = json_encode(['expiration' => $expiration, 'conditions' => $conditions]);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = [];
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = base64_encode(json_encode($callback_param));
        $response['dir'] = $dir;
        $response['random_name'] = bin2hex(openssl_random_pseudo_bytes(16));

        return $response;
    }

    //即使回调函数抛出异常，oss的文件也不会此被删除，但前端会看到oss反馈的回调错误信息
    static public function callback()
    {
        $fileinfo = request()->all();

        $pubKey = file_get_contents(base64_decode($_SERVER['HTTP_X_OSS_PUB_KEY_URL']));

        $body = file_get_contents('php://input');

        $path = $_SERVER['REQUEST_URI'];

        $pos = strpos($path, '?');

        if ($pos === false)
            $authStr = urldecode($path) . "\n" . $body;
        else
            $authStr = urldecode(substr($path, 0, $pos)) . substr($path, $pos, strlen($path) - $pos) . "\n" . $body;

        $authorization = base64_decode($_SERVER['HTTP_AUTHORIZATION']);

        if (openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5))
            return ['status' => 0, 'fileinfo' => $fileinfo];
        else
            return ['status' => 1, 'msg' => '签名失败'];
        
    }

}