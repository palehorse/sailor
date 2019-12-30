<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loader;
use Sailor\Core\Interfaces\Loaded;

class ControllerLoader implements Loader
{
	/** @param Controller */
	private $controller;

	public static function create()
	{
		return new ControllerLoader;
	}

	public function load(Loaded $ControllerFile)
	{
		$this->controller = $ControllerFile->resolve();
		return $this;
	}

	public function getController()
	{
		return $this->controller;
	}
}