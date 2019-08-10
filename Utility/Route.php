<?php

namespace Sailor\Utility;

class Route
{
    public static function version($filename)
	{
		if (preg_match('/^.*\.css|js$/', $filename)) {
			$filename = preg_replace('/^(.*)\.(css|js)/', '$1-'. str_replace('.', '', microtime(true)) . '.$2', $filename);
		}
		return $filename;
	}
}