## install

### 安装
```
composer create-project dcrswoole/framework skeleton
```

配置好`.env` 文件


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
php artisan test
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

