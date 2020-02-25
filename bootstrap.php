<?php

require_once __DIR__ . '/vendor/autoload.php';

use Sailor\Core\Config;

Config::init();

/** Environment Setup */
switch (Config::get('project.ENV')) {
    default:
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(0);
        break;
    case 'development':
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        break;
}

/** PHP Setup */
date_default_timezone_set("Asia/Taipei");
mb_internal_encoding('UTF-8');
session_start();
ini_set('session.cookie_path', sprintf('/%s/', Config::get('project.NAME')));
ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']);
ini_set('session.cookie_lifetime', 3600); 