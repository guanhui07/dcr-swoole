# dcr-swoole框架 - 整合各种包，模仿laravel hyperf 骨架 实现的框架

- 集成 laravel orm , restful route, redis, guzzle monolog
- http websocket
- rabbitmq
- container
- event
- middleware  中间件注解
- validate
- monolog
- collection
- carbon
- dotenv
- 支持路由注解 中间件注解

### 安装
```
composer create-project dcrswoole/framework
```

### 分层 (demo未按此方式)
controller -> service ->repository->model

### http:

```
php ./bin/start.php http:start 
```

### websocket:

```
php ./bin/start.php ws:start 
```

### console:

```
php artisan test2
```

### crontab:

```
/config/crontab.php  enable 改为 true 开启
```

### migrate:

```
php migrate.php  migrations:generate
php migrate.php migrations:migrate

```

### container

```
ApplicationContext::getContainer()
或 di()


```

## 路由注解和中间件注解
```php
    #[RequestMapping(methods: "GET , POST" , path:"/api/json")]
    #[Middlewares(AuthMiddleware::class , TestMiddleware::class)]
    public function test()
    {
        return 'hello';
    }
```

### 更多例子

/app/Controller/TestController.php

### composer依赖组件

```
    "doctrine/event-manager": "^1.1",  事件监听
    "doctrine/migrations": "^3.5",  migrate
    "elasticsearch/elasticsearch": "7.16",  es
    "firebase/php-jwt": "^6.3",   jwt token 
    "gregwar/captcha": "^1.1",  captcha 
    "guanhui07/database": "^1.0",   laravel orm 改
    "guanhui07/dcr-swoole-crontab": "^1.0",  crontab
    "guanhui07/guzzle": "^1.0",   guzzle client 
    "guanhui07/redis": "^1.0",   redis pool
    "inhere/console": "^4.1",    console command 
    "inhere/php-validate": "^2.8",   validate 验证器
    "intervention/image": "^2.7",   image操作
    "middlewares/utils": "^3.0",    middleware中间件
    "monolog/monolog": "^2.8",     monolog  
    "mwangithegreat/faker": "^1.9",   faker造数据
    "nesbot/carbon": "^2.6",     carbon time
    "nikic/fast-route": "^1.3",   nikic的 resful route
    "opis/closure": "^3.6",      闭包序列化
    "php-amqplib/php-amqplib": "dev-master",   rabbitmq
    "php-di/php-di": "^7.0",   依赖注入 di container 
    "qiniu/php-sdk": "^7.7",  七牛cdn
    "spatie/image": "^2.2",   
    "symfony/finder": "^5.0",   symfony finder
    "vlucas/phpdotenv": "^5.4"  dotenv读取 
```

## 关联

参考 hyperf laravel webman 项目

https://github.com/guanhui07/dcr  fpm以及workerman实现websocket

https://github1s.com/walkor/webman-framework

https://github1s.com/hyperf/hyperf

https://github1s.com/laravel/laravel

https://github.com/SerendipitySwow/Serendipity-job

https://github.com/sunsgneayo/annotation 路由注解参考


### todo:
类似`hyperf`实现 Command Crontab AutoController Cacheable 等注解

