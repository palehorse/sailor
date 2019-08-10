<?php
namespace Sailor\Core\Services;

use \ReflectionMethod;
use \ReflectionParameter;
use Sailor\Core\Loaders\MethodLoader;
use Sailor\Core\LoggerFactory as Logger;

class Method
{
    private $parameters = [];
    private $args = [];
    public function __construct($class, $method, $args=[])
    {
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

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getSerializedParameters()
    {
        $args = [];
        foreach ($this->parameters as $param) {
            $args[] = $param;
        }
        return $args;
    }

    public function injectValues()
	{
		foreach ($this->parameters as $name => $class) {
			if (!empty($class)) {
                $requiredVars = [];
                if (method_exists($class, '__construct')) {
                    $requiredVars = MethodLoader::create($class, '__construct')
                                                ->resolve()
                                                ->injectValues()
                                                ->getParameters();
                }
				$this->parameters[$name] = (new \ReflectionClass($class))->newInstanceArgs($requiredVars);
			} else {
				$this->parameters[$name] = array_shift($this->args);
			}
        }
        return $this;
	}
}