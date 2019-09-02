<?php

namespace Sailor\Core;

class Controller
{
	const CONTROLLERS_NAMESPACE = 'Sailor\\Controllers\\';

	protected $request;
	protected $response;
	protected $view;
	protected $getVars;
	protected $postVars;

	public static function create($class, $request, $response)
	{
		$ReflectionClass = new \ReflectionClass($class);
		return $ReflectionClass->newInstanceArgs([$request, $response]);
	}

	public static function getNamespace()
	{
		return self::CONTROLLERS_NAMESPACE;
	}

	public function __construct($request, $response)
	{
		$this->request = $request;
		$this->response = $response;
		$this->parseVars();
	}

	public function setView($view)
	{
		$this->view = $view;
	}

	public function view($file, array $data=[])
	{
		if (!preg_match('/\.php$/', $file)) {
			$file .= '.php';
		}
		return $this->view->render($this->response, $file, $data);
	}

	public function get($name)
	{
		if (isset($this->getVars[$name])) {
			return $this->getVars[$name];
		}
		return null;
	}

	public function post($name)
	{
		if (isset($this->postVars[$name])) {
			return $this->postVars[$name];
		}
		return null;
	}

	public function jsonResponse(array $data=[])
	{
		$this->response->withHeader('Content-type', 'application/json');
		$this->response->write(json_encode($data));
	}

	protected function parseVars()
	{
		$this->getVars = $this->request->getQueryParams();
		$this->postVars = $this->request->getParsedBody();
	}
}