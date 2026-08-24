# Biny Modernization Changelog

> 本轮改造遵循 **保留轻量 + 外部 API 100% 兼容** 原则，只做安全、正确性与工程化的关键升级。
> 详细路线图见此文件末尾。

## v2.11.0 (2026-08-24)

> 用 AI 更新了遗留的 bug 以及 PHP 8+ 兼容，算是弥补了之前的遗憾。

### 🔒 Security — P0

#### 1. Prepared statement 通道 + 真实转义
- **`lib/models/Database.php`**：新增两个方法
  - `Database::escape($string)`：调用 `mysqli_real_escape_string`（**字符集感知**），取代原来的 `addslashes`；`$handler === null` 时降级到 `addslashes` 保底
  - `Database::prepared($sql, $binds, $mode)`：`?` 占位符 + `bind_param`，全参数化查询
- **`lib/models/DAO.php:real_escape_string()`**：内部改为调用 `Database::escape()`，无 DB 连接时降级到 `addslashes`
- **`lib/models/DAO.php:real_like_string()`**：同样切到 `real_escape_string`，之前只用 `addslashes` 是遗漏
- **连接编码**：`Database::__construct` 用 `set_charset()` 替代 `SET NAMES`，让 `mysqli_real_escape_string` 正确感知字符集
- **影响**：修复非 UTF-8（GBK 等）多字节注入风险；DAO 高层 API 无需修改

#### 2. CSRF Token 密码学安全化
- **`lib/business/Request.php:generateCsrf()`**：`mt_rand()` → `random_bytes()` + `bin2hex`
- **`lib/business/Request.php:validateCsrfToken()`**：`$token === $trueToken` → `hash_equals()`（常量时间对比，防时序攻击）

#### 3. Session Cookie 反序列化加固
- **`lib/business/Request.php:validateCsrfToken()`**：`unserialize()` 加 `['allowed_classes' => false]`，禁止对象注入；对空 cookie / 格式异常提前返回 false

### 🐛 Bug Fix — P0

#### 4. `Form::typeDatetime` 逻辑颠倒
- **`lib/data/Form.php`**：原来 `if (date($format, $time) !== $str) $match = true`，导致**任何输入都能通过**校验。修正为 `===`，与 `typeDate` 语义对齐
- 已用 `tests/p0_fixes.php` 验证：合法 datetime 通过、非法字符串拒绝、纯日期（无时间部分）拒绝

#### 5. `Autoload` 稳定性
- **`lib/Autoload.php`**：
  - `include $path` → `include_once $path`，防止同一请求多次触发 loading 时重复定义类
  - `array_diff_assoc` 前对 `namespace / file` 子键做 `??` 兜底，消除 PHP 8 undefined-index warning
  - 首次 loading 时把 `$loaders` 初始化为 `['namespace'=>[], 'file'=>[]]`

#### 6. `BinyException` PHP 8 兼容
- **`lib/exception/BinyException.php`**：`fmt_code()` 与 `__construct()` 内对 `$params` 做 `is_array` 归一化处理
- 修复了大量老代码 `throw new BinyException(1006, $name)`（传字符串）在 PHP 8+ 触发 `vsprintf(...): array expected` TypeError 的问题
- `fmt_code` 对未知 code、占位符不匹配等异常路径增加兜底

#### 7. `App::__get` 顺序依赖 + 内建组件降级
- **`lib/App.php`**：`registerDefaults()` 顺序前移到 `Event::init()` 之前（否则 Logger 在 Event 注册时访问 `App::$base->config` 会因 bindings 未注册而 fatal）
- 双保险：`__get` 里新增 `builtinFactory()` 降级路径，即便 `registerDefaults` 未跑或被覆盖，内建组件（config/request/response/session/router/cache/redis/memcache/socket/app_config）仍可正常获取

#### 8. `lib/App.php` 顶部 include 改为 include_once
- 与 Composer classmap 或用户重复 include 并存时，防止类重定义 fatal

#### 9. Request CLI 分支的 undefined array key
- **`lib/business/Request.php:getUrl()`/`getBaseUrl()`**：`$argv[1]` 未定义时降级为空串
- **`lib/business/Request.php:getContentType()`**：`$_SERVER['CONTENT_TYPE']` 未定义时降级为空串
- 修复 PHP 8+ 下的 undefined array key warning

### 🧹 Code Quality — P2

#### 10. `App::$base->__get` 从硬 switch 改为注册表
- **`lib/App.php`**：新增 `App::bind($name, $factory, $singleton=true)` / `App::has($name)` 静态方法
- 内建组件（config/request/response/session/redis/memcache/socket/router/cache/app_config）通过 `registerDefaults()` 声明式注册
- 用户可扩展第三方组件（例如 mongo/kafka）而无需修改核心：
  ```php
  App::bind('mongo', fn() => new MongoDB\Client(...));
  App::$base->mongo->selectDatabase('foo');
  ```
- **向下兼容**：外部访问语法完全不变（`App::$base->request` 等），也仍抛 `BinyException(1006)`

#### 11. `Controller::dispatcher()` 冗余分支清理
- 三个 `echo $result` 分支合并为一句（Response / JSONResponse 都实现了 `__toString`）

### 📦 Ecosystem — P1

#### 12. Composer 支持
- 新增 `composer.json`：
  - 声明 `php >= 8.1`
  - `app\*` 全部走 **PSR-4**（`app\controller\` → `app/controller/` 等）
  - `App` 类通过 classmap 载入
  - `lib/functions.php` 通过 `files` 自动加载，集中放全局函数
- **Biny 自研 Autoload 保留**：`lib/` 内部的类名（如 `biny\lib\DAO` 位于 `lib/models/DAO.php`）不符合 PSR-4 规范，继续由 `biny\lib\Autoload` 兜底扫描
- Composer 与 Biny Autoload **共存**：`Autoload::init()` 会自动 `include vendor/autoload.php`

#### 13. 全局函数 `_L()` 去重
- 抽到 `lib/functions.php`，`App.php` / `Response.php` 里保留 `function_exists('_L')` 守卫版本，确保未接入 Composer 的老项目仍能工作

### 🧪 Tests

新增 `tests/` 目录，两个可直接 `php` 执行的集成脚本：
- **`tests/smoke.php`**：验证 App 启动、bind 注册表、__get 降级、_L 全局函数、未知组件抛异常 — **6/6 pass**
- **`tests/p0_fixes.php`**：验证 P0 修复点（Form 校验、CSRF 相关随机与常量对比、unserialize 禁类、BinyException 兼容、DAO 转义降级）— **14/14 pass**

后续会用 PHPUnit 重写为标准测试；本阶段保持零依赖以便任何环境跑通。

### 📋 Verification

- ✅ 所有修改文件通过 `php -l` 语法检查（15 个文件）
- ✅ 两个集成测试全部通过，无 warning、无 undefined key
- ✅ 外部 API 无变更：`App::$base->xxx`、`DAO::get()`、`filter()->find()`、`Response::display()` 等调用方式完全一致

---

## Roadmap（后续版本）

### v3.0-beta（P1 剩余）
- 关键文件加 `declare(strict_types=1)` 与完整类型声明
- `Database::FETCH_TYPE_*` / `Form::type*` 常量迁移为 PHP 8.1 `enum`（保留常量别名）
- `Request` / `Response` 补类型 hint

### v3.0（P2 剩余）
- `DAO::find/query/update/delete` 消除 `func_get_args()` 隐藏参数，改为显式 `?Cond $cond = null`
- Config 环境判断加 `getenv('APP_ENV')` 兜底
- 统一时区/编码到 config

### v3.1（P3 观测性）
- Logger 支持结构化 JSON 输出
- Request 自动分配 `X-Trace-Id` 并透传到 Logger
- Logger 实现 `Psr\Log\LoggerInterface`
- App 实现 `Psr\Container\ContainerInterface`
- 增加 PHPUnit + PHPStan 与 GitHub Actions CI

### v3.2（P4 可选）
- 中间件语法糖（基于现有 Event 系统）
- Swoole/Workerman 常驻入口 `web/server.php`（不动主线代码）
- Attribute 路由（PHP 8）

### 明确不做
- ❌ 不引入 IoC 容器（Symfony/Laravel/PHP-DI）
- ❌ 不引入 PSR-7（Request/Response 重写代价过大）
- ❌ 不引入模板引擎（Twig/Blade）
- ❌ 不追求完整 ORM，保留 DAO/Filter 链式差异化优势
