<?php

namespace Sailor\Core\Files;

use \RuntimeException;
use Sailor\Core\Interfaces\File;

class ConfigFile implements File 
{
	const EXIT_ON_ERROR = 1;
	const IGNORE_ON_ERROR = 2;

	private $handler;
	private $dir;
	private $name;
	private $content = [];
	private $ext;
	private $mode;

	public static function create($path)
	{
		return new ConfigFile($path);
	}

	public function __construct($path, $mode = self::EXIT_ON_ERROR) 
	{
		$this->load($path);
		$this->mode = $mode;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getExt()
	{
		return $this->ext;
	}

	private function load($path)
	{
		if (!file_exists($path)) {
			if ($this->mode == self::EXIT_ON_ERROR) {
				throw new RuntimeException("File $path does not exist!");
			} else {
				return false;
			}
		}

		if (!$this->validate($path)) {
			if ($this->mode == self::EXIT_ON_ERROR) {
				throw new RuntimeException("File format must be incorrect!");
			} else {
				return false;
			}
		}

		$pathinfo = pathinfo($path);
		$this->dir = $pathinfo['dirname'];
		$this->name = $pathinfo['filename'];
		$this->ext = $pathinfo['extension'];

		$this->handler = fopen($path, 'r');
		while ($line = fgets($this->handler)) {
			$this->content[] = $line;
		}

		fclose($this->handler);
	}

	public function validate($path) 
	{
		return preg_match('/\.config$/', $path);
	}
}