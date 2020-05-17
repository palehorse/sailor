<?php
namespace Sailor\Core\Services;

use \ReflectionMethod;
use \ReflectionParameter;
use Sailor\Core\Loaders\MethodLoader;
use Sailor\Core\Interfaces\Loaded;

class Method implements Loaded
{
    private $parameters = [];
    private $arguments = [];

    public static function create()
    {
        list($class, $method, $args) = func_get_args();
        return new Method($class, $method, $args);
    }

    public function __construct($class, $method, $args=[])
    {
        $this->arguments = $args;
        $reflectionMethod = new ReflectionMethod((is_object($class)) ? get_class($class) : $class, $method);
        foreach ($reflectionMethod->getParameters() as $param) {
            preg_match('/\[\s\<?\w+\>?\s(.*)\s\$' . $param->getName() . '\s\]/', 
                       ReflectionParameter::export([$class, $method], $param->getName(), true),
                       $matches);
            if (!empty($matches) && strtolower($matches[1]) !== 'array') {
                $this->parameters[$param->name] = $matches[1];
            } else {
                $this->parameters[$param->name] = '';
            }
        }
    }

    public function resolve()
    {
        return $this->injectValues();
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameterValues()
    {
        $args = [];
        foreach ($this->parameters as $param) {
            $args[] = $param;
        }
        return $args;
    }

    private function injectValues()
	{
		foreach ($this->parameters as $name => $class) {
			if (!empty($class)) {
                $requiredVars = [];
                if (method_exists($class, '__construct')) {
                    $requiredVars = Method::create($class, '__construct', [])->resolve()->getParameters();
                }
				$this->parameters[$name] = (new \ReflectionClass($class))->newInstanceArgs($requiredVars);
			} else {
				$this->parameters[$name] = array_shift($this->arguments);
			}
        }
        return $this;
	}
}