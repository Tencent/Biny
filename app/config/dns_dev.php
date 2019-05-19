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
    'database2' => array( // illuminate/database
        'default' => array(
            'read' => [
                [
                    'host' => '127.0.0.1',
                    'username' => 'root',
                ],
            ],
            'write' => [
                'host' => '127.0.0.1',
                'username' => 'root',
            ],
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'Biny',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ),
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
//        'client' => 'predis', // predis
//        'persistent' => true, // predis
    ),
);