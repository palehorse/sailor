<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\File;
use Sailor\Core\Files\RouteFile;
use Sailor\Core\Interfaces\Loader;
use \RuntimeException;

class RouteLoader implements Loader 
{
	private static $routefile;
	private $route;

	public static function create(RouteFile $routefile=null)
	{
		if (is_null($routefile)) {
			throw new RuntimeException("Missing the RouteFile!");
		}
		return new RouteLoader($routefile);
	}

	public function __construct(RouteFile $routefile)
	{
		$this->route = $routefile;
	}

	public function resolve()
	{
		$this->have($this->route->getContent());
	}

	private function have($file)
	{
		require_once $file;
	}
}