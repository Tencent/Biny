<?php
return array(
    'database' => array(
        'host'     => '127.0.0.1',
        'database' => 'Biny',
        'user'     => 'root',
        'password' => 'root',
        'encode' => 'utf8',
        'port' => 3306,
        'keep-alive' => true,
    ),
    'slaveDb' => array(
        'host'     => '127.0.0.1',
        'database' => 'Biny',
        'user'     => 'root',
        'password' => 'root',
        'encode' => 'utf8',
        'port' => 3306,
    ),
    'memcache' => array(
        'host' => '127.0.0.1',
        'port' => 12121,
        'keep-alive' => true,
    ),
    'memcache2' => array(
        'host' => '127.0.0.1',
        'port' => 21212,
        'keep-alive' => true,
    ),
    'redis' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'keep-alive' => true,
    ),
);