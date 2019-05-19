<?php
return array(
    'database' => array(
        'host'     => '127.0.0.1',
        'database' => 'Biny',
        'user'     => 'root',
        'password' => 'root',
        'encode' => 'utf8',
        'port' => 3306,
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
        'host' => 'localhost',
        'port' => 12121
    ),
    'redis' => array(
        'host' => 'localhost',
        'port' => 6379,
//        'client' => 'predis', // predis
//        'persistent' => true, // predis
    ),
);