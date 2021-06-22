<?php

$dbDev = [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=172.16.254.64;dbname=udh-connect;port=3306',
        'username' => 'root',
        'password' => 'root_db',
        'charset' => 'utf8',
    ],
    // 'db' => [
    //     'class' => 'yii\db\Connection',
    //     'dsn' => 'mysql:host=localhost;dbname=udh-connect;port=3307',
    //     'username' => 'root',
    //     'password' => '',
    //     'charset' => 'utf8',
    // ],
    'mssql' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlsrv:Server=192.168.0.3;Database=UDTest',
        'username' => 'homc',
        'password' => 'homc',
    ],
    'db_queue' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=172.16.254.64;dbname=queue;port=3306',
        'username' => 'root',
        'password' => 'root_db',
        'charset' => 'utf8',
    ],
    // 'db_queue' => [
    //     'class' => 'yii\db\Connection',
    //     'dsn' => 'mysql:host=localhost;dbname=queue-udon;port=3307',
    //     'username' => 'root',
    //     'password' => '',
    //     'charset' => 'utf8',
    // ],
];

$dbProd = [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=db;dbname=udh-connect;port=3306',
        'username' => 'root',
        'password' => 'root_db',
        'charset' => 'utf8',
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 60,
        'schemaCache' => 'cache',
    ],
    'mssql' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlsrv:Server=192.168.0.1;Database=UDON2',
        'username' => 'homc',
        'password' => 'homc',
    ],
    'db_queue' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=db;dbname=queue;port=3306',
        'username' => 'root',
        'password' => 'root_db',
        'charset' => 'utf8',
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 60,
        'schemaCache' => 'cache',
    ],
];

return YII_ENV_DEV ? $dbDev : $dbProd;
// return [
//     'class' => 'yii\db\Connection',
//     'dsn' => 'mysql:host=localhost;dbname=udh-connect;port=3307',
//     'username' => 'root',
//     'password' => '',
//     'charset' => 'utf8',
// ];