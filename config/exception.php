<?php
return array(
    /* core error */
    1000 => 'System Error [%s]',
    1001 => 'App path[%s] is not readable.',
    1002 => 'Config file[%s] is not exists.',
    1003 => 'Autoload file[%s] is fails',
    1004 => 'Autoload registry handler is fail',
    1005 => 'Permission Error: AutoLoad File[%s] can not write',
    1006 => 'APP has no property [%s]',
    1007 => 'logs list [%s] not exist or has no permission to write',

    /* controller error */
    2001 => 'Action[%s] is not exists.',
    2002 => 'Method [%s] not exists in [%s]',
    2003 => 'param Key [%s] checkType Error; %s given',
    2004 => 'access error',
    2005 => 'template [%s] is not Exist',
    2006 => 'Shell[%s] is not exists.',

    /* DAO error */
    3001 => 'Connect mysql[%s] is fail',
    3002 => 'DoubleDAO load Error: %s',
    3003 => 'Filter DAO must be DAO or DoubleDAO; %s given',
    3004 => 'param Filter Error; %s given',
    3005 => 'Filter / Merge must be the same DAO',
    3006 => 'First Filter must be Array; %s given',
    3007 => 'Filter cannot be empty',
    3008 => 'slave DataBase[%s] must be same to the master DataBase[%s]',
    3009 => 'method [%s] not exists in [%s]',
    3010 => 'group field can not be empty in [%s]',
    3011 => 'addition Key[%s] invalid',
    3012 => 'filter(not in) must be Array, %s given',

    /* Connect error */
    4001 => 'socket Create Error: [%s]',
    4002 => 'Socket Connect[%s:%s] Error',
    4003 => 'Socket Len Error',
    4004 => 'Memcache Connect Error [%s:%s]',
    4005 => 'Redis Connect Error [%s:%s]',

    /* lib error */
    5001 => '[%s] not in form[%s] values',
    5002 => 'check Method[%s] not exists in form[%s]',
    5003 => 'event class[%s] not exists',

    /* web error */
    6000 => 'cannot read request uri',
    6001 => 'privilege [%s] is not access [%s]',

    /* model error */
    7000 => 'model[%s] can not be callable',

    /* custom error */
    8000 => 'custom Error',
);