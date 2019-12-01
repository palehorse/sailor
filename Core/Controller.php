<?php

namespace Sailor\Core;

use Slim\Flash\Messages;

class Controller
{
	const CONTROLLERS_NAMESPACE = 'Sailor\\Controllers\\';

	protected $request;
	protected $response;
	protected $view;
	protected $commonData;
	protected $flash;
	protected $getVars;
	protected $postVars;
	protected $logger;

	public static function create($class, $request, $response)
	{
		$ReflectionClass = new \ReflectionClass($class);
		return $ReflectionClass->newInstanceArgs([$request, $response]);
	}

	public function __construct($request, $response)
	{
		$this->request = $request;
		$this->response = $response;
		$this->flash = new Messages;
		$this->logger = Route::getLogger();
		$this->parseVars();

		$this->commonData = [
			'title' => Config::get('project.NAME'),
		];
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

		return $this->view->render($this->response, $file, array_merge($this->commonData, $data));
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

	public function files($name=null)
	{
		if (!is_null($name)) {
			return isset($_FILES[$name]) ? $_FILES[$name] : null;
		}
		return $_FILES;
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

	protected function showError($title, $message)
    {
        $this->view('error', [
            'title' => $title, 
            'message' => $message,]);
    }
}