<?php
require_once __DIR__ . '/../bootstrap.php';

use Sailor\Core\Config;
use Sailor\Core\Route;
use Sailor\Core\LoggerFactory as Logger;

Logger::create(Config::get('project.NAME'), Config::get('project.LOG_PATH'));
Logger::select(Config::get('project.NAME'));

Route::setSlimApp(new Slim\App([
    'settings' => [
        'addContentLengthHeader' => false,
    ]
]));
Route::setLogger(Logger::getLogger());
Route::loadRoutes();
Route::getSlimApp()->run();
