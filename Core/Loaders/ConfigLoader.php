<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loader;
use Sailor\Core\Files\ConfigFile;
use \RuntimeException;

class ConfigLoader implements Loader
{
	private static $configfile;

	private $file;
	private $content;
	private $data;

	public static function create(ConfigFile $configfile=null)
	{
		if (is_null($configfile)) {
			throw new RuntimeException("Missing the ConfigFile");
		}
		return new ConfigLoader($configfile);
	}

	public function __construct(ConfigFile $configfile)
	{
		$this->format = '/^([A-Z_]+)=([\w\-_\/\.@]+)$/';
		$this->file = $configfile;
		$this->content = $this->file->getContent();
		$this->data = [];
	}

	public function resolve()
	{
		$this->parse();
		return $this;
	}

	public function getData()
	{
		return $this->data;
	}

	private function parse()
	{
		foreach ($this->content as $idx => $row) {
			preg_match($this->format, $row, $matches);
			list($original, $key, $value) = $matches;
			$this->data[$key] = $value;
		}
	}

	public function validate()
	{
		if (!preg_match($this->format, $this->content)) {
			return false;
		}
	}
}