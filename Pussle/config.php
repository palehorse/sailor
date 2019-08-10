<?php
use Sailor\Core\Config;

require_once __DIR__ . '/../bootstrap.php';

return [
    'db'       => Config::get('database.DB_ADAPTER'),
    'host'     => Config::get('database.DB_HOST'),
    'dbname'   => Config::get('database.DB_NAME'),
    'username' => Config::get('database.DB_USER'),
    'password' => Config::get('database.DB_PWD'),
    'charset'  => Config::get('database.DB_CHARSET'),
];