<?php

use Sailor\Core\Router;
use Slim\App;

Router::get('/', function() {
	echo 'Welcome';
});