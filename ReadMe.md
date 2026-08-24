## Biny

[![license](https://img.shields.io/badge/license-BSD3-blue.svg?style=flat)](https://github.com/Tencent/Biny/blob/master/LICENSE.TXT)
[![Release Version](https://img.shields.io/github/v/release/Tencent/Biny?color=red&label=release)](https://github.com/Tencent/Biny/releases)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-777bb3.svg)](https://www.php.net/)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/Tencent/Biny/pulls)

Biny是一款高性能的轻量级PHP框架

遵循 MVC 模式，用于快速开发现代 Web 应用程序

Biny代码简洁优雅，对应用层，数据层，模板渲染层的封装简单易懂，能够快速上手使用

高性能，框架响应时间在1ms以内

Biny is a high-performance lightweight PHP framework.

It follows the MVC pattern for rapid development of modern Web applications.

Biny's code is simple and elegant. Its application, data, and template-rendering layers are wrapped in a straightforward, easy-to-understand way — making it quick to pick up.

Biny is high performance: the framework's own response time is under 1ms by default, and single-machine QPS easily reaches 3000+.


## 功能介绍 Function introduction

支持跨库连表，条件复合筛选，查询PK缓存等

支持RESTful，类的自动化加载管理

支持Form表单验证，支持事件触发机制

支持浏览器端调试，快速定位程序问题和性能瓶颈

具有sql防注入，html自动防xss等特性


Support cross-database JOINs, compound conditional filtering, primary-key query caching, etc.

Supports RESTful routing and automatic class loading.

Supports Form validation and an event-trigger mechanism.

Supports in-browser debugging for quickly identifying issues and performance bottlenecks.

Built-in SQL injection prevention and automatic XSS escaping.


## 使用文档 documents

Wiki URL：[http://www.billge.cc](http://www.billge.cc)

English Wiki URL：[http://www.billge.cc?lang=en](http://www.billge.cc?lang=en)

GitHub URL：[https://github.com/Tencent/Biny](https://github.com/Tencent/Biny)

## 第三方组件接入 Extensions

从 v2.11.0 起，Biny 支持通过 `App::bind()` 注册表挂载任意第三方组件，无需修改框架核心。以下是几份实战指南：

- 📖 [**MongoDB 集成**](docs/mongodb.md) — 直接挂 `MongoDB\Client`，或封装 MongoDAO 风格类
- 📖 [**JWT / JWE 集成**](docs/jwt.md) — 用 `firebase/php-jwt` + `beforeAction` 事件做无状态鉴权（前后端分离常用）

同样的方式可用于 Kafka / Elasticsearch / RabbitMQ / S3 等任意第三方服务。示例：

```php
App::bind('kafka', fn() => new RdKafka\Producer(...));
App::$base->kafka->produce(...);
```

Since v2.11.0, Biny supports mounting any third-party component via the `App::bind()` registry — no core changes needed.

- 📖 [MongoDB integration](docs/mongodb.md)
- 📖 [JWT / JWE integration](docs/jwt.md)

## FAQ

Q: 框架跟传统PHP框架区别在哪儿，有什么优势？

A: Biny是个自由度很高的框架，不像其他框架需要配置各种路由，依赖外部组件。这些在Biny中都是不需要的，按照一个简单的规则就能快速使用这些功能。同时框架已集成了自动加载机制，从开发者的角度出发，在功能上使用非常简单。而且具有相当强的安全性。从框架层面完全屏蔽了 SQL注入和 XSS注入两大安全难题，非常适合新人使用。

Q: Biny框架的性能如何？

A: 测试机：Intel Xeon Processor E5506 (4M Cache, 2.13 GHz, 4.80 GT/s Intel QPI)
一个普通查询数据页面（50%命中缓存）QPS 能轻松达到3000以上，同比Yii，性能是Yii的2倍以上。

Q: 我想使用Biny，请问有相关说明文档吗？

A: 文档都在[http://www.billge.cc](http://www.billge.cc)中

Q: Biny框架适配PHP7吗？

A: 从 v2.11.0 起，Biny 已完全适配 PHP 8.x（推荐 PHP 8.1+），修复了原有的 PHP 8 兼容问题与若干安全漏洞（详见 [CHANGELOG.md](CHANGELOG.md)）。同时保持向下兼容 PHP 7.x，切换到最新版仅需替换 lib 库。

Q: Biny现在是最终版了吗，还会继续更新吗？

A: 目前版本在多个项目中已经正常使用，相对成熟。后续会针对性能和功能上都会持续更新，届时只需更新替换 lib库 即可使用最新框架。

Q: What is the difference between a framework and a traditional PHP framework? What are the advantages?

A: Biny is a highly flexible framework. Unlike other frameworks that require extensive route configuration or external components, Biny needs none of that — a simple convention gets you going. Class autoloading is built in, making the framework very easy to use from a developer's perspective. Security is strong by default: SQL injection and XSS are blocked at the framework layer, making Biny especially friendly to newcomers.

Q: What is the performance of the Biny framework?

A: Testing machine: Intel Xeon Processor E5506 (4M Cache, 2.13 GHz, 4.80 GT/s Intel QPI).
A typical data-query page (50% cache hit rate) easily reaches 3000+ QPS — more than 2x the throughput of Yii under the same conditions.

Q: Does the Biny framework work with PHP7?

A: Since v2.11.0, Biny is fully compatible with PHP 8.x (PHP 8.1+ recommended). Previous PHP 8 compatibility issues and several security bugs have been fixed — see [CHANGELOG.md](CHANGELOG.md) for details. Backward compatibility with PHP 7.x is preserved; upgrading only requires replacing the lib directory.

Q: Is Biny the final version now? Will it continue to be updated?

A: The current version has been running in multiple production projects and is considered stable. It will continue to receive performance and feature updates — upgrading is as simple as replacing the `lib/` directory.



## 常见问题 Common Problems

Q：模版渲染出现错乱是为什么

A：请在php.ini中打开short_open_tag。Biny的示例中使用了PHP中原生的简写渲染方法，需要将系统配置中的简写配置打开才能正常使用。
当然如果是自己开发的模版页面，不用简写方式的话，就算不打开short_open_tag也是可以的。简写示例：
```
<?php echo $string;?> => <?=$string?>
```

Q: Why does the template render as garbled output?


A: Please enable `short_open_tag` in `php.ini`. Biny's built-in examples use PHP's short-tag rendering syntax, which requires this setting to be enabled.
  If your own template files don't use the short-tag form, `short_open_tag` doesn't need to be enabled. Short-tag example:
```
<?php echo $string;?> => <?=$string?>
```

