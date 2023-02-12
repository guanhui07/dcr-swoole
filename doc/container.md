### container 容器

### 实现原理
容器，支持依赖注入，底层为反射 ,本框架使用的`php-di/php-di` composer包 


### 获取容器
```php
ApplicationContext::getContainer()
或 di()


```

## 从容器 拿对象 获取 参数
```php
//->all()  ->get()  ->post() 等方法
ApplicationContext::getContainer()->get(Request::class)->all();
//di()->(Request::class)->all();
```

