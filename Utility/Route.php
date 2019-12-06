<?php

namespace Sailor\Utility;

use Sailor\Core\Route as Router;

class Route
{
    public static function version($filename)
	{
		$ext = 'css|js|jpg|jpeg|png|gif';
		if (preg_match('/^.*\.(' . $ext . ')$/', $filename)) {
			$filename = preg_replace('/^(.*)\.(' . $ext . ')/', '$1-'. str_replace('.', '', microtime(true)) . '.$2', $filename);
		}
		return $filename;
	}

	public static function pathFor($name) 
	{
		return Router::pathFor($name);
	}
}