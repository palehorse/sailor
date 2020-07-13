<?php
namespace Sailor\Core;

class Config
{
	const MAX_LINE_LENGTH = 1000;
	public static $configs = [];

	/**
	 * Return the value of Config from config files
	 * 
	 * @param string $key
	 * @return mixed|null
	 */
	public static function get($key)
	{
		if (!preg_match('/^(\w+)\.([A-Z0-9_]+)$/', $key, $matches)) {
			return null;
		}

		if (!isset(self::$configs[$matches[1]])) {
			$filename = sprintf(__DIR__ . '/../config/%s.config', $matches[1]);
			if (!file_exists($filename)) {
				return null;
			}

			$fp = fopen($filename, 'r');

			while ($row = fgets($fp, self::MAX_LINE_LENGTH)) {
				list($name, $value) = explode('=', $row);
				self::$configs[$name] = preg_match('/^([\w]+,)+([\w]+)$/', $value) ? explode(',', $value) : trim($value);
			}
		}

		return isset(self::$configs[$matches[2]]) ? self::$configs[$matches[2]] : null;
	}
}