<?php
namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loader;
use Sailor\Core\Interfaces\Loaded;
use Sailor\Core\Services\Method;

class MethodLoader implements Loader
{
    public static function create()
    {
        return new MethodLoader;
    }

    public function load(Loaded $Method)
    {
        return $Method->resolve();
    }
}