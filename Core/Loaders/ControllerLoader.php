<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loader;
use Sailor\Core\Controller;
use RuntimeException;

class ControllerLoader implements Loader
{
	private $class;
	private $controller;
	private $request;
	private $response;

	public static function create($classname='', 
								  $request=null, 
								  $response=null)
	{
		if (empty($classname)) {
			throw new RuntimeException("Missing the Class name");
		}

		if (is_null($request)) {
			throw new RuntimeException("Missing the Request");
		}

		if (is_null($response)) {
			throw new RuntimeException("Missing the Response");
		}

		return new ControllerLoader($classname, $request, $response);
	}

	public function __construct($classname, $request, $response)
	{
		$this->class = $classname;
		$this->request = $request;
		$this->response = $response;
	}

	public function resolve()
	{
		$this->controller = Controller::create($this->class, 
								  			   $this->request, 
								  			   $this->response);
		return $this->controller;

	}
}