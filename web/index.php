<?php
date_default_timezone_set('Asia/Shanghai');

defined('SYS_DEBUG') or define('SYS_DEBUG', true);
defined('SYS_CONSOLE') or define('SYS_CONSOLE', true);
//dev pre pub
defined('SYS_ENV') or define('SYS_ENV', 'dev');
defined('isMaintenance') or define('isMaintenance', false);

if (SYS_DEBUG) {
    ini_set('display_errors', 'On');
}
error_reporting(E_ALL ^ E_NOTICE);

$loader = realpath(__DIR__ . '/../vendor/autoload.php');
if (file_exists($loader)) {
    include $loader;
}

include __DIR__ . '/../lib/App.php';

//include __DIR__.'/../lib/XHProf.php';
//XHProf::start();

App::registry(realpath(__DIR__ . '/../app'));
App::run();

//$data = XHProf::end();
//XHProf::display($data);