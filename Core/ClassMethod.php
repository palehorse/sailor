<?php
namespace Sailor\Core;

class ClassMethod 
{
    /** @var mixed */
    private $instance;

    /** @var string */
    private $method;

    /** @var array */
    private $parameters = [];

    /**
     * @param string $class
     * @param string $method
     * @param array $parameters
     */
    public function __construct($class, $method, array $parameters=[])
    {
        $this->class = $class;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     * Return the name with the namespace of Class
     * 
     * @return string
     */
    public function getName()
    {
        return (new \ReflectionClass($this->class))->getName();
    }

    /**
     * Return the class name
     * 
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Return the method of Class
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return the parameters
     * 
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}