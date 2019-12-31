<?php

namespace Sailor\Core;

use Sailor\Core\Files\ControllerFile;
use Sailor\Core\Files\HookFile;
use Sailor\Core\Loaders\ControllerLoader;
use Sailor\Core\Loaders\MethodLoader;
use Sailor\Core\Loaders\RouteLoader;
use Sailor\Core\Files\RouteFile;
use Sailor\Core\Files\ViewExtensionFile;
use Sailor\Core\Loaders\HookLoader;
use Slim\App;
use Sailor\Core\Loaders\ViewLoader;
use Sailor\Core\Services\Method;

class Route
{
	const CONTROLLER_PATH = __DIR__ . '/../Controllers';
	const CONTROLLER_NAMESPACE = 'Sailor\\Controllers\\';
	const ROUTE_PATH = __DIR__ . '/../routes/';

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

	public static function getLogger()
	{
		return self::$app->getContainer()->get('logger');
	}

	public static function loadRoutes() 
	{
		$files = self::glob();
		foreach ($files as $file) {
			RouteLoader::create()->load(RouteFile::create($file));
		}
	}

	public static function pathFor($name)
	{
		return self::$app->getContainer()
						 ->get('router')
						 ->pathFor($name);
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
			$Callable = !is_string($Callable) ? $Callable : ControllerFile::CONTROLLER_NAMESPACE . $Callable;

			if (!is_string($Callable) && is_callable($Callable)) {
				return call_user_func_array($Callable, $args);
			}
			
			if (preg_match("/(.*)::([\w\-]+)/", $Callable, $matches) && is_callable($Callable))
			{
				list($raw, $class, $action) = $matches;
				$extensions = glob(__DIR__ . '/../Extensions/Twig/{*.php}', GLOB_BRACE);
				foreach ($extensions as $ext) {
					$ViewLoader = ViewLoader::create();
					$ViewLoader->load(ViewExtensionFile::create($ext));
				}

				$controllerFileName = str_replace(self::CONTROLLER_NAMESPACE, '', $class);
				$ControllerLoader = ControllerLoader::create()->load(ControllerFile::create(self::CONTROLLER_PATH . '/' . $controllerFileName . '.php', $request, $response));
				
				$controller = $ControllerLoader->getController();
				$controller->setView(ViewLoader::getTwig());
				
				self::$controller = $controller;
				self::$action     = $action;

				$hooks = glob(__DIR__ . '/../hooks/{*.php}', GLOB_BRACE);
				foreach ($hooks as $hook) {
					$HookLoader = HookLoader::create();
					$HookLoader->load(HookFile::create($hook));
					$controller->addHookLoader($HookLoader);
				}
				$controller->runHooks();

				$parameters = MethodLoader::create()->load(Method::create($class, $action, $args))->getParameterValues();
				
				return self::execute($parameters);
			}
		});
	}

	public static function notFound($template, $title, $message, $desc)
	{
		$view = ViewLoader::getTwig();
		$params = [
			'title' => $title,
			'message' => $message,
			'desc' => $desc,
		];
		$container = self::$app->getContainer();
		$container['notFoundHandler'] = function($c) use ($view, $template, $params) {
			return function ($request, $response) use ($c, $view, $template, $params) {
				return $view->render($response->withStatus(404), $template, $params);
			};
		};
	}

	public static function error($template, $title, $message, $desc)
	{
		$view = ViewLoader::getTwig();
		$params = [
			'title' => $title,
			'message' => $message,
			'desc' => $desc,
		];
		$container = self::$app->getContainer();
		$logger = $container['logger'];
		$container['errorHandler'] = function($c) use ($view, $template, $params, $logger) {
			return function ($request, $response, $e) use ($c, $view, $template, $params, $logger) {
				$logger->error($e->getMessage());
				return $view->render($response->withStatus(500), $template, $params);
			};
		};
	}

	private static function execute($parameters=[])
	{
		return call_user_func_array([self::$controller, self::$action], $parameters);
	}

	private static function glob()
	{
		return glob(self::ROUTE_PATH . '{*.php}', GLOB_BRACE);
	}
}