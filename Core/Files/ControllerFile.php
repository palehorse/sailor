<?php
namespace Sailor\Core\Files;

use Sailor\Core\Interfaces\File;
use RuntimeException;
use Sailor\Core\Controller;

class ControllerFile implements File
{
	const EXT = 'php';
	const CONTROLLER_NAMESPACE = 'Sailor\\Controllers\\';

	private $dir;
	private $basename;
	private $name;
	private $ext;
	private $request;
	private $response;

	public static function create()
	{
		$argv = func_get_args();
		if (empty($argv) || !is_string($argv[0])) {
			throw new RuntimeException('The path of the file is required.');
		}

		

		list($path, $request, $response) = $argv;
		return new ControllerFile($path, $request, $response);
    }
    
    public function __construct($path, $request, $response) 
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

		$this->request = $request;
		$this->response = $response;
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
		$class = self::CONTROLLER_NAMESPACE . $this->name;
		$ReflectionClass = new \ReflectionClass($class);
		return $ReflectionClass->newInstanceArgs([$this->request, $this->response]);
	}
}