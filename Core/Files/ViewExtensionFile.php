<?php
namespace Sailor\Core\Files;

use Sailor\Core\Interfaces\File;
use RuntimeException;

class ViewExtensionFile implements File
{
	const EXT = 'php';
	const EXTENSION_NAMESPACE = 'Sailor\\Extensions\\Twig\\';

	private $dir;
	private $basename;
	private $name;
	private $ext;

	public static function create()
	{
		$argv = func_get_args();
		if (empty($argv) || !is_string($argv[0])) {
			throw new RuntimeException('The path of the file is required.');
		}

		$path = array_shift($argv);
		return new ViewExtensionFile($path);
    }
    
    public function __construct($path) 
	{
		if (!file_exists($path)) {
			throw new RuntimeException('The file: ' . $path . ' does not exist');
		}

		$info = pathinfo($path);
		if ($info['extension'] != self::EXT) {
			throw new RuntimeException('The file extension must be .' . self::EXT);
		}

		$this->dir = $info['dirname'];
		$this->basename = $info['basename'];
		$this->name = $info['filename'];
		$this->ext = $info['extension'];
	}

	/**
	 * @return string
	 */
	public function getDir()
	{
		return $this->dir;
	}

	/**
	 * @return string
	 */
	public function getBaseName()
	{
		return $this->basename;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getExt()
	{
		return $this->ext;
	}

	public function resolve()
	{
		$class = self::EXTENSION_NAMESPACE . $this->name;
		return new $class;
	}
}