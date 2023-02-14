
## Log 日志
dcr使用 [monolog/monolog](https://github.com/Seldaek/monolog) 处理日志。


```php
use DcrSwoole\Log\Log;
   
#[Inject]
protected Log $logger;

$this->logger->info('fggg'); 
//info debug info error 等方法
```




