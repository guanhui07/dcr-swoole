### container

```
ApplicationContext::getContainer()
或 di()


```

## 从容器 拿对象 获取 参数
```php
//->all()  ->get()  ->post() 等方法
ApplicationContext::getContainer()->get(Request::class)->all();
//di()->(Request::class)->all();
```

