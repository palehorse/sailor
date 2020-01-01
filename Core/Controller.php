<?php

namespace Sailor\Core;

use Slim\Flash\Messages;
use Sailor\Core\Loaders\HookLoader;

class Controller
{
	protected $request;
	protected $response;
	protected $view;
	protected $hookLoaders = [];
	protected $data = [];
	protected $flash;
	protected $getVars;
	protected $postVars;
	protected $logger;

	public function __construct($request, $response)
	{
		$this->request = $request;
		$this->response = $response;
		$this->flash = new Messages;
		$this->logger = Route::getLogger();
		$this->parseVars();
	}

	public function setView($view)
	{
		$this->view = $view;
	}

	public function addHookLoader(HookLoader $HookLoader)
	{
		$this->hookLoaders[] = $HookLoader;
	}

	public function runHooks()
	{
		foreach ($this->hookLoaders as $HookLoader) {
			$HookLoader->call($this->data);
		}
	}

	protected function view($file, array $data=[])
	{
		if (!preg_match('/\.php$/', $file)) {
			$file .= '.php';
		}

		return $this->view->render($this->response, $file, array_merge($this->data, $data));
	}
	
	protected function get($name)
	{
		if (isset($this->getVars[$name])) {
			return $this->getVars[$name];
		}
		return null;
	}

	protected function post($name)
	{
		if (isset($this->postVars[$name])) {
			return $this->postVars[$name];
		}
		return null;
	}

	protected function files($name=null)
	{
		if (!is_null($name)) {
			return isset($_FILES[$name]) ? $_FILES[$name] : null;
		}
		return $_FILES;
	}

	protected function jsonResponse(array $data=[])
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