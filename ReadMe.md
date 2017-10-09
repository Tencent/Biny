## 概况 general situation
Biny是一款高性能的超轻量级PHP框架

遵循 MVC 模式，用于快速开发现代 Web 应用程序

Biny代码简洁优雅，对应用层，数据层，模板渲染层的封装简单易懂，能够快速上手使用

高性能，框架响应时间在1ms以内，单机qps轻松上3000

Biny is a high performance lightweight PHP framework

Follow the MVC pattern for rapid development of modern Web applications

Biny code is simple and elegant, and the application layer, data layer, template rendering layer of the package is simple and easy to understand, and it can be used quickly

High performance, framework response time of less than 1ms, stand-alone QPS easily up to 3000


## 功能介绍 Function introduction

支持跨库连表，条件复合筛选，查询PK缓存等

同步异步请求分离，类的自动化加载管理

支持Form表单验证，支持事件触发机制

支持浏览器端调试，快速定位程序问题和性能瓶颈

具有sql防注入，html自动防xss等特性


Support cross library join table, conditional compound filter, query PK cache, etc.

Synchronous asynchronous request separation, automatic loading management of classes

Supports Form validation and supports event triggering mechanisms

Supports browser side debugging, rapid positioning problems and performance bottlenecks

With SQL anti injection, HTML automatic, anti XSS and other characteristics


## 使用文档 documents

Wiki URL：[http://www.billge.cc](http://www.billge.cc)

GitHub URL：[https://github.com/Tencent/Biny](https://github.com/Tencent/Biny)

## FAQ

Q: 框架跟传统PHP框架区别在哪儿，有什么优势？

A: Biny是个自由度很高的框架，不像其他框架需要配置各种路由，自动加载类，复杂的命名空间。这些在Biny中都是不需要的，按照一个简单的规则就能快速使用这些功能。从开发者的角度出发，在功能上使用非常简单。而且具有相当强的安全性。从框架层面完全屏蔽了 SQL注入和 XSS注入两大安全难题，非常适合新人使用。

Q: Biny框架的性能如何？

A: 测试机：Intel Xeon Processor E5506 (4M Cache, 2.13 GHz, 4.80 GT/s Intel QPI)
一个普通查询数据页面（50%命中缓存）QPS 能轻松达到3000以上，同比Yii，性能是Yii的2倍以上。

Q: 我想使用Biny，请问有相关说明文档吗？

A: 文档都在[http://www.billge.cc](http://www.billge.cc)中

Q: Biny框架适配PHP7吗？

A: 可以完美运行，性能提高2倍以上。

Q: Biny现在是最终版了吗，还会继续更新吗？

A: 目前版本在多个项目中已经正常使用，相对成熟。后续会针对性能和功能上都会持续更新，届时只需更新替换 lib库 即可使用最新框架。

Q: What is the difference between a framework and a traditional PHP framework? What are the advantages?

A: Biny is a framework with high degree of freedom, unlike other frameworks that need to configure various routes, automatically load classes, and complex namespaces. These are unnecessary in Biny and can be quickly used with a simple rule. From the developer's point of view, the function is very simple to use. And it has fairly strong security. From the framework level, completely shielding the SQL injection and XSS injection two security problems, very suitable for new use.

Q: What is the performance of the Biny framework?

A: Testing machine: Intel Xeon Processor E5506 (4M Cache, 2.13 GHz, 4.80 GT/s Intel QPI)
A common query data page (50% hit cache), QPS can easily reach more than 3000, compared to Yii, Yii performance is more than 2 times.

Q: Does the Biny framework fit PHP7?

A: Can be perfect operation, performance increased by more than 2 times.

Q: Is Biny the final version now, and will it continue to be updated?

A: The current version has been used in several projects and is relatively mature. The follow-up will be updated for both performance and functionality, and you'll need to update and replace the Lib library to use the latest framework.



## 常见问题 Common problem

Q：模版渲染出现错乱是为什么

A：请在php.ini中打开short_open_tag。Biny的示例中使用了PHP中原生的简写渲染方法，需要将系统配置中的简写配置打开才能正常使用。
当然如果是自己开发的模版页面，不用简写方式的话，就算不打开short_open_tag也是可以的。简写示例：
```
<?php echo $string;?> => <?=$string?>
```

Q：Why is the template rendering deranged?


A：Please open short_open_tag in php.ini. In the example of Biny, you use the native abbreviated rendering method in PHP, and you need to open the short configuration in the system configuration for normal use.
  Of course, if you are developing your own template page, you don't need to abbreviate it, even if you don't open the short_open_tag. Abbreviated example:
```
<?php echo $string;?> => <?=$string?>
```

