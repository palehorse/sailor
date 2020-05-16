<?php
namespace Sailor\Utility;

use Sailor\Core\Route as Router;

class Logger
{
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(Router::getLogger(), $name)) {
            return call_user_func_array([Router::getLogger(), $name], $arguments);
        }
    }
}