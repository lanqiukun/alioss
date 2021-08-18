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
