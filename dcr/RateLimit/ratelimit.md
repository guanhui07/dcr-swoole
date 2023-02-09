


```php

class RateLimit implements MiddlewareInterface
{

     public function handle(): Closure
    {
        return static function ($request, $next) {
            $throttler = ApplicationContext::getContainer()->get(RateLimitHandler::class);
            // “桶”可以容纳的请求数
            $capacity       =  60;
            // 	“桶”完全重新装满所需的时间
            $seconds        =  60;
            // “桶”此操作使用的令牌数
            $cost           =  1;

            if ($throttler->handle($request->getRemoteIp(), $capacity, $seconds, $cost) === false) {
                throw new RuntimeException('请求次数太频繁');
            }

            return $next->handle($request);
        };

    }
}


```

