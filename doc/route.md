## 路由


当您想改变某个请求路由时请更改配置文件 `config/routes.php`。

如果你想关闭默认路由，在配置文件 `config/routes.php`里最后一行加上如下配置：


## 闭包路由
`config/routes.php`里添加如下路由代码
```php
Route::any('/test', function ($request) {
    return apiSuccess('test');
});

```
> **注意**
> 由于闭包函数不属于任何控制器，所以`$request->app` `$request->Controller` `$request->action` 全部为空字符串。

当访问地址为 `http://127.0.0.1:8787/test` 时，将返回`test`字符串。

> **注意**
> 路由路径必须以`/`开头，例如

```php
// 错误的用法
Route::any('test', function ($request) {
    return apiSuccess('test');
});

// 正确的用法
Route::any('/test', function ($request) {
    return apiSuccess('test');
});
```


## 类路由
`config/routes.php`里添加如下路由代码
```php
Route::any('/testclass', [app\Controller\IndexController::class, 'test']);
```
当访问地址为 `http://127.0.0.1:8787/testclass` 时，将返回`app\Controller\IndexController`类的`test`方法的返回值。


## 路由参数
如果路由中存在参数，通过`{key}`来匹配，匹配结果将传递到对应的控制器方法参数中(从第二个参数开始依次传递)，例如：
```php
// 匹配 /user/123 /user/abc
Route::any('/user/{id}', [app\Controller\UserController::class, 'get']);
```
```php
namespace app\Controller;
class UserController
{
    public function get($request, $id)
    {
        return apiSuccess('接收到参数'.$id);
    }
}
```

更多例子：
```php
// 匹配 /user/123, 不匹配 /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return apiSuccess($id);
});

// 匹配 /user/foobar, 不匹配 /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return apiSuccess($name);
});

// 匹配 /user /user/123 和 /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return apiSuccess($name ?? 'tom');
});
```

## 路由分组

有时候路由包含了大量相同的前缀，这时候我们可以用路由分组来简化定义。例如：

```php
Route::group('/blog', function () {
   Route::any('/create', function ($rquest) {return apiSuccess('create');});
   Route::any('/edit', function ($rquest) {return apiSuccess('edit');});
   Route::any('/view/{id}', function ($rquest, $id) {return apiSuccess("view $id");});
});
```
等价与
```php
Route::any('/blog/create', function ($rquest) {return apiSuccess('create');});
Route::any('/blog/edit', function ($rquest) {return apiSuccess('edit');});
Route::any('/blog/view/{id}', function ($rquest, $id) {return apiSuccess("view $id");});
```

group嵌套使用

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($rquest) {return apiSuccess('create');});
      Route::any('/edit', function ($rquest) {return apiSuccess('edit');});
      Route::any('/view/{id}', function ($rquest, $id) {return apiSuccess("view $id");});
   });  
});
```

## 路由中间件

我们可以给某个一个或某一组路由设置中间件。
例如：
```php
Route::any('/admin', [app\admin\Controller\IndexController::class, 'index'])->middleware([
    app\Middleware\MiddlewareA::class,
    app\Middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return apiSuccess('create');});
   Route::any('/edit', function () {return apiSuccess('edit');});
   Route::any('/view/{id}', function ($r, $id) {apiSuccess("view $id");});
})->middleware([
    app\Middleware\MiddlewareA::class,
    app\Middleware\MiddlewareB::class,
]);
```

> **注意**:
> `->middleware()` 路由中间件作用于 group 分组之后时候，当前路由必须在处于当前分组之下

```php
# 错误使用例子

Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($rquest) {return apiSuccess('create');});
      Route::any('/edit', function ($rquest) {return apiSuccess('edit');});
      Route::any('/view/{id}', function ($rquest, $id) {return apiSuccess("view $id");});
   });  
})->middleware([
    app\Middleware\MiddlewareA::class,
    app\Middleware\MiddlewareB::class,
]);

```

```php
# 正确使用例子
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($rquest) {return apiSuccess('create');});
      Route::any('/edit', function ($rquest) {return apiSuccess('edit');});
      Route::any('/view/{id}', function ($rquest, $id) {return apiSuccess("view $id");});
   })->middleware([
    app\Middleware\MiddlewareA::class,
    app\Middleware\MiddlewareB::class,
    ]);  
});
```

## 资源型路由
```php
Route::resource('/test', app\Controller\IndexController::class);

//指定资源路由
Route::resource('/test', app\Controller\IndexController::class, ['index','create']);

//非定义性资源路由
// 如 notify 访问地址则为any型路由 /text/notify或/text/notify/{id} 都可 routeName为 test.notify
Route::resource('/test', app\Controller\IndexController::class, ['index','create','notify']);
```
| Verb   | URI                 | Action   | Route Name    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |




## url生成
> **注意**
> 暂时不支持group嵌套的路由生成url

例如路由：
```php
Route::any('/blog/{id}', [app\Controller\BlogController::class, 'view'])->name('blog.view');
```
我们可以使用如下方法生成这个路由的url。
```php
route('blog.view', ['id' => 100]); // 结果为 /blog/100
```

视图里使用路由的url时可以使用此方法，这样不管路由规则如何变化，url都会自动生成，避免因路由地址调整导致大量更改视图文件的情况。


## 获取路由信息
> **注意**
> 需要 dcr-framework >= 1.3.2

通过`$request->route`对象我们可以获取当前请求路由信息，例如

```php
$route = $request->route; // 等价与 $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // 此特性需要 dcr-framework >= 1.3.16
}
```

> **注意**
> 如果请求没有匹配任何路由(默认路由除外)，则`$request->route`为null


## 处理404
当路由找不到时默认返回404状态码并输出`public/404.html`文件内容。

如果开发者想介入路由未找到时的业务流程，可以使用dcr提供的回退路由`Route::fallback($callback)`方法。比如下面的代码逻辑是当路由未找到时重定向到首页。
```php
Route::fallback(function(){
    return redirect('/');
});
```
再比如当路由不存在时返回一个json数据，这在dcr作为api接口时非常实用。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

相关连接 [自定义404 500页面](others/custom-error-page.md)

## 路由接口
```php
// 设置$uri的任意方法请求的路由
Route::any($uri, $callback);
// 设置$uri的get请求的路由
Route::get($uri, $callback);
// 设置$uri的请求的路由
Route::post($uri, $callback);
// 设置$uri的put请求的路由
Route::put($uri, $callback);
// 设置$uri的patch请求的路由
Route::patch($uri, $callback);
// 设置$uri的delete请求的路由
Route::delete($uri, $callback);
// 设置$uri的head请求的路由
Route::head($uri, $callback);
// 同时设置多种请求类型的路由
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// 分组路由
Route::group($path, $callback);
// 资源路由
Route::resource($path, $callback, [$options]);
// 回退路由，设置默认的路由兜底
Route::fallback($callback);
```
如果uri没有对应的路由(包括默认路由)，且回退路由也未设置，则会返回404。
