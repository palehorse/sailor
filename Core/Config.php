<?php

namespace Sailor\Core;

use Sailor\Core\Files\ConfigFile;
use Sailor\Core\Loaders\ConfigLoader;

class Config 
{
	private static $path = __DIR__ . '/../config/';
	private static $data = [];
	public static function init()
	{
		$files = self::glob(self::$path);
		foreach ($files as $file) {
			$config = ConfigFile::create($file);
			self::$data[$config->getName()] = ConfigLoader::create($config)->resolve()->getData();
		}
	}

	public static function get($key)
	{
		if (!preg_match('/(\w+)\.(\w+)/', $key, $matches)) {
			return null;
		}

		list($original, $name, $subname) = $matches;
		return isset(self::$data[$name][$subname]) ? 
			   self::$data[$name][$subname] : null;
	}

	private static function glob()
	{
		return glob(self::$path . '{*.config}', GLOB_BRACE);
	}
}