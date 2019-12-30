<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loaded;
use Sailor\Core\Interfaces\Loader;
use \RuntimeException;

class RouteLoader implements Loader 
{
	public static function create()
	{
		return new RouteLoader;
	}

	public function load(Loaded $routeFile)
	{
		return $routeFile->resolve();
	}
}