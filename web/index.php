<?php
date_default_timezone_set('Asia/Shanghai');

if (preg_match("/cli/i", php_sapi_name())) {
    die("if you want to run in shell model, you can use:\nphp shell.php <router> <param>\n");
}

defined('SYS_DEBUG') or define('SYS_DEBUG', true);
defined('SYS_CONSOLE') or define('SYS_CONSOLE', true);
//dev pre pub
defined('SYS_ENV') or define('SYS_ENV', 'dev');
defined('isMaintenance') or define('isMaintenance', false);

if (SYS_DEBUG) {
    ini_set('display_errors', 'On');
}
error_reporting(E_ALL ^ E_NOTICE);

include __DIR__ . '/../lib/App.php';

//include __DIR__.'/../lib/XHProf.php';
//XHProf::start();

App::registry(realpath(__DIR__ . '/../app'));
App::run();

//$data = XHProf::end();
//XHProf::display($data);