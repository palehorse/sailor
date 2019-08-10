<?php

namespace Sailor\Core\Files;

use Sailor\Core\Interfaces\File;

class ControllerFile implements File 
{
	const EXIT_ON_ERROR = 1;
	const IGNORE_ON_ERROR = 2;

	private $basepath = __DIR__ . '/../routes';
	private $handler;
	private $dir;
	private $name;
	private $content;
	private $ext;

	public static function create($path)
	{
		return new RouteFile($path);
	}

	public function __construct($path) 
	{
		$this->load($path);
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

	public function validate($path) 
	{
		return preg_match('/\.php$/', $path);
	}

	private function load($path) 
	{
		if (!file_exists($path)) {
			throw new RuntimeException("Controller does not exist!");
		}

		$pathinfo = pathinfo($path);
		$this->dir = $pathinfo['dirname'];
		$this->name = $pathinfo['filename'];
		$this->ext = $pathinfo['extension'];

		$this->content = $path;
	}
}