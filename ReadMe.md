## Biny

[![license](http://img.shields.io/badge/license-BSD3-blue.svg?style=flat)](https://github.com/tencent/biny/blob/master/LICENSE.TXT)
[![Release Version](https://img.shields.io/badge/release-2.7.2-red.svg)](https://github.com/tencent/biny/releases)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/tencent/biny/pulls)

Biny是一款高性能的超轻量级PHP框架

遵循 MVC 模式，用于快速开发现代 Web 应用程序

Biny代码简洁优雅，对应用层，数据层，模板渲染层的封装简单易懂，能够快速上手使用

高性能，框架响应时间在1ms以内，单机qps轻松上3000

Biny is a high performance lightweight PHP framework.

It follows the MVC pattern for rapid development of modern Web applications

Biny code is simple and elegant. The application layer, data layer, and template rendering layer of the package is simple and easy to understand. This makes it quick to pick up.

Biny is high performance. Framework comes default with response time of less than 1ms. Stand-alone QPS easily up to 3000.


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

A: Biny是个自由度很高的框架，不像其他框架需要配置各种路由，依赖外部组件。这些在Biny中都是不需要的，按照一个简单的规则就能快速使用这些功能。同时框架已集成了自动加载机制，从开发者的角度出发，在功能上使用非常简单。而且具有相当强的安全性。从框架层面完全屏蔽了 SQL注入和 XSS注入两大安全难题，非常适合新人使用。

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

A: Biny is a framework with high degree of freedom, unlike other frameworks that need to configure various routes, relying on external components. These are unnecessary in Biny and can be quickly used with simple rules. At the same time, the framework has been integrated with the autoload mechanism. From the developer's point of view, the functionality is very simple to use. It defaults to strong security. From the framework level, the applications is completely shielded from SQL injection and XSS injection.

Q: What is the performance of the Biny framework?

A: Testing machine: Intel Xeon Processor E5506 (4M Cache, 2.13 GHz, 4.80 GT/s Intel QPI)
A common query data page (50% hit cache), QPS can easily reach more than 3000. This is twice as fast as Yii.

Q: Does the Biny framework work with PHP7?

A: Yes. Performance increases have been seen of over 2x when compared with PHP5.

Q: Is Biny the final version now? Will it continue to be updated?

A: The current version has been used in several projects and is relatively mature. The follow-up will be updated for both performance and functionality, and you'll need to update and replace the Lib library to use the latest framework.



## 常见问题 Common Problems

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

