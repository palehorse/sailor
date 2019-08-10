<?php
require_once __DIR__ . '/bootstrap.php';

use Sailor\Core\Config;

$env = Config::get('database.DB_ENV');

$baseDBConfig = [];
$baseDBConfig['adapter']   = Config::get('database.DB_ADAPTER');
$baseDBConfig['host']      = Config::get('database.DB_HOST');
$baseDBConfig['name']      = Config::get('database.DB_NAME');
$baseDBConfig['user']      = Config::get('database.DB_USER');
$baseDBConfig['pass']      = Config::get('database.DB_PWD');
$baseDBConfig['post']      = Config::get('database.DB_PORT');
$baseDBConfig['charset']   = Config::get('database.DB_CHARSET');
$baseDBConfig['collation'] = Config::get('database.DB{_COLLATION');

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
    ],
    'environments' => [
        'default_database' => $env,
        'development' => array_merge($baseDBConfig, []),
        'production'  => array_merge($baseDBConfig, []),
    ]
];