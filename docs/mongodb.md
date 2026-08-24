# 在 Biny 里挂载第三方组件（以 MongoDB 为例）

> 从 v2.11.0 开始，Biny 支持通过 `App::bind()` 注册表挂载任意第三方组件，无需修改框架核心。
> 本文以 MongoDB 为例，演示两种典型接入方式。同样的方式可用于 Kafka / Elasticsearch / RabbitMQ 等。

## 前置条件

```bash
# 1. 安装 MongoDB PHP 驱动扩展
pecl install mongodb
# 或 macOS: brew install php-mongodb

# 2. 安装官方封装库
composer require mongodb/mongodb
```

在 `php.ini` 里启用扩展：`extension=mongodb.so`。

---

## 方式 1：直接挂载 MongoDB\Client（最简单）

**Step 1** — 在应用配置里加 mongo 配置。编辑 `app/config/database.php`：

```php
return array(
    'dbConfig' => array( /* MySQL 配置保持原样 */ ),
    'mongo' => array(
        'uri'      => 'mongodb://127.0.0.1:27017',
        'database' => 'app',
    ),
);
```

**Step 2** — 在入口文件里注册组件。编辑 `web/index.php`：

```php
include __DIR__ . '/../lib/App.php';
App::registry(realpath(__DIR__ . '/../app'));

// ★ 新增：挂载 mongo（单例）
App::bind('mongo', function () {
    $cfg = App::$base->app_config->get('mongo', 'database');
    return (new \MongoDB\Client($cfg['uri']))->selectDatabase($cfg['database']);
});

App::run();
```

**Step 3** — 业务里直接用，跟 `App::$base->redis` 一模一样：

```php
$users = App::$base->mongo->selectCollection('users');
$users->insertOne(['name' => 'foo', 'age' => 20]);
$doc = $users->findOne(['name' => 'foo']);
```

---

## 方式 2：封装成 MongoDAO 风格（推荐用于稍大项目）

如果你想让 MongoDB DAO 与 Biny 的 MySQL DAO 保持一致的调用风格（`find/query/insert/update/delete/count/paginate`），把下面这个类放到 `extends/MongoDAO.php`（Biny Autoload 会自动扫到）：

```php
<?php
// extends/MongoDAO.php
namespace app\dao;

use App;

class MongoDAO
{
    protected $collection;         // 子类覆盖：集合名
    protected $conn = 'mongo';     // 绑定名，可指向不同实例（主/从、多库）
    private $col;

    public function __construct()
    {
        $this->col = App::$base->{$this->conn}->selectCollection($this->collection);
    }

    public function find(array $filter = [], array $options = []): ?array
    {
        $doc = $this->col->findOne($filter, $options);
        return $doc ? (array) $doc : null;
    }

    public function query(array $filter = [], array $options = []): array
    {
        return array_map(
            fn($d) => (array)$d,
            iterator_to_array($this->col->find($filter, $options))
        );
    }

    public function insert(array $doc): string
    {
        return (string) $this->col->insertOne($doc)->getInsertedId();
    }

    public function update(array $filter, array $set): int
    {
        return $this->col->updateMany($filter, ['$set' => $set])->getModifiedCount();
    }

    public function delete(array $filter): int
    {
        return $this->col->deleteMany($filter)->getDeletedCount();
    }

    public function count(array $filter = []): int
    {
        return $this->col->countDocuments($filter);
    }

    public function paginate(int $size, int $page = 0, array $filter = []): array
    {
        return $this->query($filter, ['limit' => $size, 'skip' => $page * $size]);
    }

    public function aggregate(array $pipeline): array
    {
        return array_map(fn($d) => (array)$d, iterator_to_array($this->col->aggregate($pipeline)));
    }
}
```

然后每个业务集合定义一个薄薄的 DAO 类：

```php
<?php
// app/dao/userMongoDAO.php
namespace app\dao;

class userMongoDAO extends MongoDAO
{
    protected $collection = 'users';
}
```

在 Controller / Service 里：

```php
$dao = new \app\dao\userMongoDAO();
$id = $dao->insert(['name' => 'foo', 'age' => 20]);
$doc = $dao->find(['name' => 'foo']);
$dao->update(['name' => 'foo'], ['age' => 21]);
$list = $dao->paginate(10, 0, ['age' => ['$gt' => 18]]);
```

---

## 高级：多个 Mongo 实例（分库 / 主从）

```php
App::bind('mongoMain', function () {
    $cfg = App::$base->app_config->get('mongoMain', 'database');
    return (new \MongoDB\Client($cfg['uri']))->selectDatabase($cfg['database']);
});
App::bind('mongoLog', function () {
    $cfg = App::$base->app_config->get('mongoLog', 'database');
    return (new \MongoDB\Client($cfg['uri']))->selectDatabase($cfg['database']);
});

// 业务 DAO 只需指定 conn：
class logMongoDAO extends MongoDAO {
    protected $collection = 'access_log';
    protected $conn = 'mongoLog';   // ← 指向不同实例
}
```

---

## 同理可挂载

只要遵循「工厂函数返回实例」的模式，任何第三方组件都能这样接进来：

```php
App::bind('kafka',   fn() => new RdKafka\Producer(...));
App::bind('es',      fn() => Elasticsearch\ClientBuilder::create()->build());
App::bind('rabbit',  fn() => new PhpAmqpLib\Connection\AMQPStreamConnection(...));
App::bind('s3',      fn() => new Aws\S3\S3Client(...));
```

`App::$base->kafka`、`App::$base->es` 等即用即取，Biny 内核零改动。

---

## 为什么框架本身不内置 MongoDB？

Biny 的定位是**轻量级**框架 —— 内核只放"每个 web 项目都必须要的最小集合"（路由、请求、响应、模板、MySQL DAO、Session/CSRF、日志、事件）。数据库驱动（Mongo / PostgreSQL / SQLite / Redis Cluster / Kafka …）作为可选扩展，通过 `App::bind()` 挂载是更合适的方式：

- ✅ 用户不用某个组件时，`composer.json` 里就没这个依赖，代码体积/攻击面都最小
- ✅ 不同项目可以用不同版本的 driver，不受框架约束
- ✅ 框架不用为每个 driver 出兼容补丁

如果你觉得自己封装的 `MongoDAO` 类质量不错，欢迎提 PR 到 `extends/` 目录（社区扩展），这样其他人也能用。
