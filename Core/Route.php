<?php

namespace Sailor\Core;

use Sailor\Core\Loaders\ControllerLoader;
use Sailor\Core\Loaders\MethodLoader;
use Sailor\Core\Loaders\RouteLoader;
use Sailor\Core\Files\RouteFile;
use Slim\App;
use Sailor\Core\Loaders\ViewLoader;

class Route
{
	private static $path = __DIR__ . '/../routes/';
	private static $controller;
	private static $action;
	private static $app;

	public static function setSlimApp(App $app)
	{
		self::$app = $app;
	}

	public static function getSlimApp()
	{
		return self::$app;
	}
	
	public static function setLogger($logger)
	{
		$container = self::$app->getContainer();
		$container['logger'] = function($c) use ($logger) {
			return $logger;
		};

		$container['notFoundHandler'] = function($c) use ($logger) {
			return function($request, $response) use ($c, $logger) {
				$logger->error('Page Not Found');
			};
		};
	}

	public static function loadRoutes() 
	{
		$files = self::glob();
		foreach ($files as $file) {
			RouteLoader::create(RouteFile::create($file))->resolve();
		}
	}

	public static function get($uri, $Callable)
	{
		return self::pass('get', $uri, $Callable);
	}

	public static function post($uri, $Callable)
	{
		return self::pass('post', $uri, $Callable);
	}

	public static function group($name, $Callable)
	{
		return call_user_func([self::$app, 'group'], $name, $Callable);
	}

	private static function pass($method, $uri, $Callable)
	{
		switch ($method) {
			case 'get':
			case 'post':
			case 'put':
			case 'delete':
				break;
			default:
				return false;
		}

		return @call_user_func([self::$app, $method], $uri, function($request, $response, $args) use ($Callable) {
			$Callable = !is_string($Callable) ? $Callable : Controller::CONTROLLERS_NAMESPACE . $Callable;

			if (!is_string($Callable) && is_callable($Callable)) {
				return call_user_func_array($Callable, $args);
			}
			
			if (preg_match("/(.*)::([\w\-]+)/", $Callable, $matches) && is_callable($Callable))
			{
				list($raw, $class, $action) = $matches;
				$ViewLoader = ViewLoader::create();

				$ControllerLoader = ControllerLoader::create($class, $request, $response);
				$controller = $ControllerLoader->resolve();
				$controller->setView($ViewLoader->resolve());
				
				self::$controller = $controller;
				self::$action     = $action;

				$parameters = MethodLoader::create($controller, $action, $args)
										  ->resolve()
									      ->injectValues()
										  ->getSerializedParameters();
				
				return self::execute($parameters);
			}
		});
	}

	private static function execute($parameters=[])
	{
		return call_user_func_array([self::$controller, self::$action], $parameters);
	}

	private static function glob()
	{
		return glob(self::$path . '{*.php}', GLOB_BRACE);
	}
}