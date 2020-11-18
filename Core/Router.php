<?php
namespace Sailor\Core;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use RuntimeException;
use Slim\Http\Response;
use Sailor\Core\Builders\ExtensionBuilder;
use Sailor\Core\Builders\HandlerBuilder;
use Sailor\Core\Interfaces\ErrorHandler;
use Slim\Views\Twig;
use Sailor\Core\Builders\ClassMethodBuilder;
use Sailor\Core\Builders\ControllerBuilder;
use Twig\Extension\AbstractExtension;

class Router 
{
    const METHODS = ['get', 'post'];

    /** @var App */
    private static $app;

    /** @var Twig */
    private static $twig;

    /**
     * Execute the method of Controller when user agent connect with GET method
     * 
     * @param string $uri
     * @param mixed $callable
     */
    public static function get($uri, $callable)
    {
        return self::callHttpMethod('get', $uri, $callable);
    }

    /**
     * Execute the method of Controller when user agent connect with POST method
     * 
     * @param string $uri
     * @param mixed $callable
     */
    public static function post($uri, $callable)
    {
        return self::callHttpMethod('post', $uri, $callable);
    }

    /**
     * Execute the method of Controller when user agent connect with PUT method
     * 
     * @param string $uri
     * @param mixed $callable
     */
    public static function put($uri, $callable)
    {
        return self::callHttpMethod('put', $uri, $callable);
    }

    /**
     * Execute the method of Controller when user agent connect with DELETE method
     * 
     * @param string $uri
     * @param mixed $callable
     */
    public static function delete($uri, $callable)
    {
        return self::callHttpMethod('delete', $uri, $callable);
    }

    public static function group($uri, $callable)
    {
        self::init();
        return self::$app->group($uri, $callable);
    }

    public static function run()
    {
        self::init();
        return self::$app->run();
    }

    public static function pathFor($name) 
    {
        self::init();
        return self::$app->getContainer()
						 ->get('router')
						 ->pathFor($name);
    }

    public static function version($filename)
    {
        $ext = 'css|js|jpg|jpeg|png|gif';
		if (preg_match('/^.*\.(' . $ext . ')$/', $filename)) {
			$filename = preg_replace('/^(.*)\.(' . $ext . ')/', '$1-'. str_replace('.', '', microtime(true)) . '.$2', $filename);
		}
		return $filename;
    }

    private static function init()
    {
        if (!self::$twig instanceof Twig) {
            self::$twig = new Twig(Config::get('project.ROOT') . Config::get('project.VIEW_PATH'));

            /** Twig Extensions */
            $extensionBuilder = new ExtensionBuilder;
            $extensions = $extensionBuilder->build();
            foreach ($extensions as $extension) {
                self::$twig->addExtension($extension);
            }
        }

        if (!self::$app instanceof \Slim\App) {
            $Container = new \Slim\Container([
                'settings' => [
                    'displayErrorDetail' => true,
                    'logger' => [
                        'name' => Config::get('project.NAME'),
                        'level' => strtoupper(Config::get('log.LEVEL')),
                        'path' => Config::get('log.PATH') . '/' . date('Y-m-d') . '.log',
                    ],
                ]
            ]);

            /** Error Handlers */
            $handlerBuilder = new HandlerBuilder(self::$twig);
            $handlers = $handlerBuilder->build();

            $Container = array_reduce($handlers, function(ContainerInterface $Container, ErrorHandler $handler) {
                $names = $handler->getName();
                if (!is_array($names)) {
                    $names = [$names];
                }

                foreach ($names as $name) {
                    if (!empty($Container[$name])) {
                        unset($Container[$name]);
                    }
    
                    $Container[$name] = $handler->buildHandler();
                }
                
                return $Container;
            }, $Container);

            self::$app = new \Slim\App($Container);
        }
    }

    /**
     * @param string $httpMethod
     * @param Callable $callable
     * @return Response
     */
    private static function callHttpMethod($httpMethod, $uri, $callable)
    {
        if (!in_array($httpMethod, ['get', 'post', 'put', 'delete'])) {
            throw new RuntimeException('HTTP Method of Route is invalid');
        }

        self::init();

        return @call_user_func([self::$app, $httpMethod], $uri, function($request, $response, $args) use ($callable) {
            if (!is_string($callable) && is_callable($callable)) {
                return call_user_func_array($callable, array_merge([$request, $response], $args));
            }

            if (!preg_match('/::[\w_]+$/', $callable)) {
                return null;
            }

            list($controllerName, $controllerMethod) = explode('::', $callable, 2);

            $segments = array_filter(explode('\\', $controllerName), function($segment) {
                return preg_match('/^[\w_]+$/', $segment);
            });

            if (count($segments) != count(explode('\\', $controllerName))) {
                return null;
            }

            return self::executeControllerMethod(
                (new ControllerBuilder(
                    $controllerName, 
                    $request, 
                    $response,
                    self::$twig
                ))->build(),
                $controllerMethod,
                $args
            );
        });
    }

    /**
     * Execute method of Controller
     * 
     * @param Controller $class
     * @param string $method
     * @param array $args
     * @return Response
     */
    private static function executeControllerMethod(Controller $controller, $method, array $args=[])
    {
        if (!method_exists($controller, $method)) {
            throw new RuntimeException('Method ' . $method . ' of ' . (new ReflectionClass($controller))->getName());
        }

        $classMethod = (new ClassMethodBuilder(
            (new ReflectionClass($controller))->getName(),
            $method,
            $args
        ))->build();

        return call_user_func_array([$controller, $method], $classMethod->getParameters());
    }
}