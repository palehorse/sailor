<?php

namespace Sailor\Core;

use Sailor\Core\Files\ConfigFile;
use Sailor\Core\Loaders\ConfigLoader;
use \RuntimeException;

class Config 
{
	private static $path = __DIR__ . '/../config/';
	private static $data = [];
	public static function init()
	{
		$files = self::glob(self::$path);
		foreach ($files as $file) {
			$config = ConfigFile::create($file);
			self::$data[$config->getName()] = ConfigLoader::create()->load($config)->getConfigData();
		}
	}

	public static function get($key)
	{
		if (!is_string($key)) {
			throw new RuntimeException('The key must be a string');
			return null;
		}

		if (preg_match('/(\w+)\.(\w+)/', $key, $matches)) {
			list($original, $name, $subName) = $matches;
		} else {
			$name = $key;
		}
		
		if (!empty($subName)) {
			if (isset(self::$data[$name])) {
				return isset(self::$data[$name][$subName]) ? self::$data[$name][$subName] : null;
			}
			return null;
		}
		
		return isset(self::$data[$name]) ? self::$data[$name] : null; 	
	}

	private static function glob()
	{
		return glob(self::$path . '{*.config}', GLOB_BRACE);
	}
}