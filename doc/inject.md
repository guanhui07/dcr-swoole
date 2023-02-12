
## Inject

# 注解

注解是  非常强大的一项功能，可以通过注解的形式减少很多的配置，以及实现很多非常方便的功能。

## 概念

### 什么是注解？

注解功能提供了代码中的声明部分都可以添加结构化、机器可读的元数据的能力， 注解的目标可以是类、方法、函数、参数、属性、类常量。 通过 反射 API 可在运行时获取注解所定义的元数据。 因此注解可以成为直接嵌入代码的配置式语言。

通过注解的使用，在应用中实现功能、使用功能可以相互解耦。 某种程度上讲，它可以和接口（interface）与其实现（implementation）相比较。 但接口与实现是代码相关的，注解则与声明额外信息和配置相关。 接口可以通过类来实现，而注解也可以声明到方法、函数、参数、属性、类常量中。 因此它们比接口更灵活。

注解使用的一个简单例子：将接口（interface）的可选方法改用注解实现。 我们假设接口 ActionHandler 代表了应用的一个操作： 部分 action handler 的实现需要 setup，部分不需要。 我们可以使用注解，而不用要求所有类必须实现 ActionHandler 接口并实现 setUp() 方法。 因此带来一个好处——可以多次使用注解。

### 实现原理

基础于php8 getAttrbutes 获取配置，底层为反射，类似hyperf的注解

## 使用注解

注解一共有 3 种应用对象，分别是 `类`、`类方法` 和 `类属性`。


## 依赖注入方式一 使用注解
```php
   #[Inject]
    public TestService $testService;
```

注意引入
`use DI\Attribute\Inject;`


## 方式二 构造方式
```php
    public TestService $testService;

    /**
     * @param TestService $testService
     */
    public function __construct(TestService $testService)
    {
        parent::__construct();
        $this->testService = $testService;
    }
```
