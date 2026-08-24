<?php
/**
 * 冒烟测试：验证 App 启动、bind 注册表、__get 降级、_L 函数不重定义
 * 不连数据库，只走内存路径。
 */

date_default_timezone_set('Asia/Shanghai');
define('SYS_DEBUG', false);
define('SYS_CONSOLE', false);
define('SYS_ENV', 'dev');
define('RUN_SHELL', true); // 走 CLI 分支，避开 request 相关 header 输出

error_reporting(E_ALL);
ini_set('display_errors', 'On');

include __DIR__ . '/../lib/App.php';

// 需要 app 目录，随便造一个能过 registry 的目录
$appPath = realpath(__DIR__ . '/../app');
if (!$appPath) {
    fwrite(STDERR, "app dir not found\n");
    exit(1);
}

try {
    App::registry($appPath);
    echo "[OK] App::registry passed\n";
} catch (\Throwable $e) {
    echo "[FAIL] App::registry: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(2);
}

// 检查 bind 注册表
$expected = ['config', 'app_config', 'request', 'response', 'session', 'redis', 'memcache', 'socket', 'router', 'cache'];
foreach ($expected as $name) {
    if (!App::has($name)) {
        echo "[FAIL] App::has('$name') should be true\n";
        exit(3);
    }
}
echo "[OK] All builtin bindings registered\n";

// 检查 __get 能拿到 config
try {
    $cfg = App::$base->config;
    if (!$cfg) throw new \Exception('config is null');
    echo "[OK] App::\$base->config accessible\n";
} catch (\Throwable $e) {
    echo "[FAIL] App::\$base->config: " . $e->getMessage() . "\n";
    exit(4);
}

// 检查自定义 bind
App::bind('myCustom', function () {
    return new \stdClass();
});
if (!App::has('myCustom')) {
    echo "[FAIL] Custom bind not registered\n";
    exit(5);
}
$obj1 = App::$base->myCustom;
$obj2 = App::$base->myCustom;
if ($obj1 !== $obj2) {
    echo "[FAIL] Singleton not respected\n";
    exit(6);
}
echo "[OK] Custom bind + singleton works\n";

// 检查 _L 函数存在且只定义一次
if (!function_exists('_L')) {
    echo "[FAIL] _L() global function not defined\n";
    exit(7);
}
echo "[OK] _L() global function defined\n";

// 检查未注册的组件抛 BinyException
// 注意：BinyException 在 RUN_SHELL=true 时会 echo + exit，无法用 try/catch 捕获
// 所以用 register_shutdown_function 检测异常是否被触发
$_GLOBALS_exceptionSeen = false;
ob_start();
register_shutdown_function(function () use (&$_GLOBALS_exceptionSeen) {
    $out = ob_get_clean();
    if (strpos($out, 'APP has no property') !== false) {
        echo "[OK] Unknown component triggers BinyException 1006 (via CLI echo/exit)\n";
        echo "\n=== ALL SMOKE TESTS PASSED ===\n";
    } else {
        echo "[FAIL] Unknown component did not trigger expected exception. Output:\n";
        echo $out . "\n";
    }
});
$x = App::$base->nonExistentComponent; // 触发 exit

