<?php
namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loader;
use Sailor\Core\Services\Method;

class MethodLoader implements Loader
{
    private $class;
    private $action;
    private $args;
    private $method;
    public static function create($class=NULL, $action='', $args=[])
    {
        if (is_null($class) || (empty($action) || !is_string($action))) {
            return null;
        }
        return new self($class, $action, $args);
    }

    public function __construct($class, $action, $args=[])
    {
        $this->class = $class;
        $this->action = $action;
        $this->args = $args;
    }

    public function resolve()
    {
        if (empty($this->method)) {
            $this->method = new Method($this->class, $this->action, $this->args);
        }
        return $this->method;
    }
}