<?php

require_once __DIR__ . '/vendor/autoload.php';

use Sailor\Core\Builders\ExtensionBuilder;
use Sailor\Core\Builders\HandlerBuilder;
use Sailor\Core\Config;
use Sailor\Core\Router;
use Slim\Views\Twig;

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
ini_set('session.name', !empty(Config::get('session.NAME')) ? Config::get('session.NAME') : Config::get('project.NAME'));
ini_set('session.cookie_path', sprintf('/%s/', !empty(Config::get('session.PATH')) ? Config::get('session.PATH') : Config::get('project.NAME')));
ini_set('session.cookie_domain', !empty(Config::get('session.DOMAIN')) ? Config::get('session.DOMAIN') : $_SERVER['HTTP_HOST']);
ini_set('session.cookie_lifetime', !empty(Config::get('session.LIFE_TIME')) ? Config::get('session.LIFE_TIME') : 3600); 

/** Routes */
foreach (glob(__DIR__ . '/routes/*.php') as $route) {
    include_once $route;
}