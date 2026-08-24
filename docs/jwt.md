# 在 Biny 中使用 JWT / JWE

> Biny 不内置 JWT/JWE 实现，但通过 v2.11.0 引入的 `App::bind()` 注册表可以非常方便地挂载社区成熟的 JWT 库。
> 本文以 [`firebase/php-jwt`](https://github.com/firebase/php-jwt)（PHP 生态下载量最大、维护活跃）为例，演示前后端分离场景下的完整用法。

## 前置：安装依赖

```bash
composer require firebase/php-jwt

# 如果要用 JWE（加密 JWT），额外装 web-token 生态
composer require web-token/jwt-library
```

---

## 一、JWT：签发 + 验证（推荐大多数场景使用）

### 1. 配置密钥

编辑 `app/config/config.php`（或建 `app/config/jwt.php`）：

```php
return array(
    // ...
    'jwt' => array(
        'secret'   => getenv('JWT_SECRET') ?: 'CHANGE_ME_IN_PRODUCTION_32bytes_min',
        'alg'      => 'HS256',   // 对称：HS256/HS384/HS512；非对称：RS256/ES256
        'issuer'   => 'biny-app',
        'audience' => 'biny-web',
        'ttl'      => 7200,      // 2 小时
    ),
);
```

> **⚠️ 强烈建议密钥走环境变量**，不要硬编码到代码库里。生产环境用 32 字节以上的随机字符串。

### 2. 封装一个 JwtService

在 `app/service/jwtService.php`：

```php
<?php
namespace app\service;

use App;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class jwtService
{
    private array $cfg;

    public function __construct()
    {
        $this->cfg = App::$base->config->get('jwt');
    }

    /**
     * 签发一个 JWT
     * @param array $claims 业务字段（如 uid、role）
     * @param int|null $ttl 自定义有效期，覆盖 config 默认值
     */
    public function issue(array $claims, ?int $ttl = null): string
    {
        $now = time();
        $payload = array_merge($claims, [
            'iss' => $this->cfg['issuer'],
            'aud' => $this->cfg['audience'],
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + ($ttl ?? $this->cfg['ttl']),
            'jti' => bin2hex(random_bytes(8)),  // 唯一 ID，可用于黑名单
        ]);
        return JWT::encode($payload, $this->cfg['secret'], $this->cfg['alg']);
    }

    /**
     * 验证并解码 JWT
     * @return array|null 成功返回 payload，失败返回 null
     */
    public function verify(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->cfg['secret'], $this->cfg['alg']));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            \biny\lib\Logger::warn('JWT expired: ' . $e->getMessage());
        } catch (SignatureInvalidException $e) {
            \biny\lib\Logger::warn('JWT signature invalid: ' . $e->getMessage());
        } catch (\Throwable $e) {
            \biny\lib\Logger::warn('JWT decode failed: ' . $e->getMessage());
        }
        return null;
    }
}
```

### 3. 在登录接口签发

`app/controller/authAction.php`：

```php
<?php
namespace app\controller;

use biny\lib\Action;

class authAction extends Action
{
    protected $csrfValidate = false;   // JWT 场景走 Authorization header，不再依赖 cookie CSRF
    protected $restApi      = true;

    public function POST_login()
    {
        $form = $this->getForm('login');
        if (!$form->check()) {
            return $this->response->error($form->getError());
        }

        // 你的登录校验逻辑
        $user = $this->userService->authenticate($form->username, $form->password);
        if (!$user) {
            return $this->response->error('用户名或密码错误');
        }

        // 签发 token
        $token = $this->jwtService->issue([
            'uid'  => $user['id'],
            'role' => $user['role'],
        ]);

        return $this->response->correct([
            'token'      => $token,
            'expires_in' => 7200,
        ]);
    }
}
```

前端拿到 token 后，后续请求带上：`Authorization: Bearer <token>`

### 4. 用 Event 钩子做统一验证（"中间件"效果）

在 `app/event/jwtEvent.php` 里注册 `beforeAction` 事件，自动校验带 `Authorization` 头的接口请求：

```php
<?php
namespace app\event;

use App;
use biny\lib\Event;
use biny\lib\Request;
use biny\lib\Logger;

class jwtEvent
{
    public static function init()
    {
        Event::on(beforeAction, [__CLASS__, 'authenticate']);
    }

    /**
     * @param string $event
     * @param Request $request
     */
    public static function authenticate($event, $request)
    {
        // 只拦截 RESTful API（其他保持原样，走 CSRF）
        $action = $request->getModule(true);
        if (!$action || !$action->getRestful()) {
            return;
        }

        $auth = $request->header('Authorization') ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            self::reject('missing bearer token');
        }

        $payload = App::$base->jwtService->verify($m[1]);
        if (!$payload) {
            self::reject('invalid or expired token');
        }

        // 把当前用户塞到 cache 里，业务代码可通过 App::$base->cache->currentUser 拿
        App::$base->cache->currentUser = $payload;
    }

    private static function reject(string $reason): void
    {
        Logger::warn("JWT auth rejected: $reason");
        header(App::$base->config->get(401, 'http'));
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['flag' => false, 'error' => 'Unauthorized: ' . $reason]);
        exit;
    }
}
```

**⚠️ 白名单**：如果登录接口本身也是 RESTful，需要在拦截前排除掉：

```php
$whitelist = [
    'auth' => ['action_POST_login', 'action_POST_register'],
];
if (isset($whitelist[$request->getModule()])
    && in_array($request->getMethod(), $whitelist[$request->getModule()])) {
    return;
}
```

### 5. 启动时注册事件 & 组件

在 `web/index.php` 里：

```php
App::registry(realpath(__DIR__ . '/../app'));

// 挂 JwtService（可选：不挂也行，Biny 会通过 Action::__get 兜底自动加载）
App::bind('jwtService', function () {
    return new \app\service\jwtService();
});

// 启动 JWT 中间件
\app\event\jwtEvent::init();

App::run();
```

### 6. 业务里用当前用户

```php
class userAction extends Action {
    protected $restApi = true;

    public function GET_me() {
        $user = App::$base->cache->currentUser;   // 中间件塞进来的
        return $this->response->correct($user);
    }
}
```

---

## 二、进阶：token 主动作废（黑名单）

JWT 是**无状态**的，签出去就管不到了。想要"登出"、"改密码后强制登出全部设备"，有两种方案：

### 方案 A：短 TTL + refresh token（推荐）

- Access token TTL 缩短到 15~30 分钟
- 额外发一个长期 refresh token（存 Redis / DB）
- 用户注销时只需把 refresh token 从 DB 删掉即可

### 方案 B：JTI 黑名单

利用上面 JwtService 里签发的 `jti`（每个 token 唯一），登出时把 `jti` 塞进 Redis 并设置 TTL = token 剩余有效期：

```php
// 登出
App::$base->redis->setex("jwt_blacklist:{$payload['jti']}", $payload['exp'] - time(), 1);

// 验证时增加一步
if (App::$base->redis->exists("jwt_blacklist:{$payload['jti']}")) {
    return null;   // 拒绝
}
```

---

## 三、JWE：加密 JWT（少数需要保密 payload 的场景）

如果 payload 里放了敏感信息（如手机号、身份证号），需要用 JWE 而非 JWT：

```bash
composer require web-token/jwt-library
```

```php
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Encryption\Algorithm\KeyEncryption\Dir;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A256GCM;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Encryption\Serializer\CompactSerializer;
use Jose\Component\Core\JWK;

$key = JWK::create([
    'kty' => 'oct',
    'k'   => base64_encode(random_bytes(32)),   // 32 字节共享密钥
]);

$jweBuilder = new JWEBuilder(
    new AlgorithmManager([new Dir()]),
    new AlgorithmManager([new A256GCM()])
);

$jwe = $jweBuilder->create()
    ->withPayload(json_encode(['uid' => 123, 'mobile' => '13800138000']))
    ->withSharedProtectedHeader(['alg' => 'dir', 'enc' => 'A256GCM'])
    ->addRecipient($key)
    ->build();

$token = (new CompactSerializer())->serialize($jwe);
```

大多数业务场景**用 JWT 就够了**，敏感字段不应该放到 token 里 —— 更好的做法是 token 里只放 uid，敏感数据每次从 DB 查。

---

## 常见坑

| 坑 | 说明 |
|---|---|
| **HS256 密钥泄露 = token 可伪造** | 密钥不能提交到代码仓库；生产建议改用 RS256（非对称，只有签名端有私钥） |
| **`iat/exp/nbf` 时间用整数秒** | 别用毫秒 |
| **CORS 场景要允许 `Authorization` header** | 在 `config.php` 的 `allowOrigin` 生效时，Response 里加 `Access-Control-Allow-Headers: Authorization, Content-Type` |
| **不要把 JWT 塞进 localStorage** | XSS 一波带走。前端建议用 httpOnly cookie 或内存变量 |
| **JWT 不适合放大量数据** | 每次请求都要传，太大浪费带宽。控制在 1KB 以内 |
| **算法名硬编码在服务端** | 别信 header 里的 `alg` 字段（历史 CVE：`alg: none` 绕过） |

---

## 为什么框架不内置？

同 [MongoDB 那份文档](./mongodb.md) 的理由 —— 框架保持轻量，第三方成熟库（`firebase/php-jwt` 有 3 亿+ 下载量，比任何框架自造轮子都可靠）通过 `App::bind()` 挂载更合适。
