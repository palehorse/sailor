<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\FileLoader;
use Sailor\Core\Files\ConfigFile;
use Sailor\Core\Interfaces\Loaded;

class ConfigLoader implements FileLoader
{
	private $configFile;
	private $data;

	public static function create()
	{
		return new ConfigLoader;
	}

	/**
	 * @param Loaded $configFile
	 */
	public function load(Loaded $configFile) 
	{
		$this->configFile = $configFile;
		$this->data = $this->configFile->resolve();
		return $this;
	}

	/**
	 * @return array
	 */
	public function getConfigData()
	{
		return $this->data;
	}
}