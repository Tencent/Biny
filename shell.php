#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-9-30
 * Time: 下午4:32
 */
date_default_timezone_set('Asia/Shanghai');
if (!preg_match("/cli/i", php_sapi_name())) {
    die('please run in shell model');
}
defined('RUN_SHELL') or define('RUN_SHELL', true);
//dev pre pub
defined('SYS_ENV') or define('SYS_ENV', 'dev');

ini_set('display_errors','On');
error_reporting(E_ALL);

include __DIR__ . '/lib/App.php';

App::registry(realpath(__DIR__ . '/app'));

App::shell();