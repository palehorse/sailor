<?php
namespace Sailor\Core\Builders;

use ReflectionClass;
use Sailor\Core\ClassMethod;

class ClassMethodBuilder
{
    /** @var string */
    private $class;

    /** @var string */
    private $method;

    /** @var array */
    private $args = [];

    /** @var array */
    private $parameters = [];

    /**
     * @param mixed $class
     * @param string $method
     * @param array $args
     */
    public function __construct($class, $method, array $args=[])
    {
        $this->class = $class;
        $this->method = $method;
        $this->args = $args;
        $this->parameters = $this->buildParameters();
    }

    /**
     * Create a instance of ClassMethod
     */
    public function build()
    {
        return new ClassMethod(
            $this->class,
            $this->method,
            $this->parameters
        );
    }

    private function buildParameters()
    {
        $parameters = method_exists($this->class, $this->method) ? array_reduce((new \ReflectionMethod($this->class, $this->method))->getParameters(), function($parameters, $parameter) {
            preg_match(
                '/\[\s\<?\w+\>?\s(.*)\s\$' . $parameter->getName() . '\s\]/', 
                \ReflectionParameter::export([$this->class, $this->method], $parameter->getName(), true),
                $matches
            );

            if (!empty($matches) && isset($matches[1])) {
                $class = $matches[1];
                $classMethod = (new ClassMethodBuilder($class, '__construct'))->build();
                $parameters[] = (new ReflectionClass($class))->newInstanceArgs($classMethod->getParameters());
            } else if (!empty($this->args)) {
                $parameters[] = array_shift($this->args);
            }

            return $parameters;
        }, $this->parameters) : [];

        return !empty($parameters) ? $parameters : [];
    }
}