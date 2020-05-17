<?php

namespace Sailor\Core;

use ErrorException;
use Sailor\Core\Loaders\HookLoader;
use Sailor\Utility\JSend;
use Slim\Exception\NotFoundException;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;

class Controller
{
	protected $request;
	protected $response;
	protected $view;
	protected $hookLoaders = [];
	protected $data = [];
	protected $getVars;
	protected $postVars;
	protected $logger;

	public function __construct($request, $response)
	{
		$this->request = $request;
		$this->response = $response;
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

	protected function parseVars()
	{
		$this->getVars = $this->request->getQueryParams();
		$this->postVars = $this->request->getParsedBody();
	}

	protected function forbidden($isJson = false)
	{
		$statusCode = 403;

		if ($isJson === true) {
			$this->showJsonError(
				'forbidden', 
				'您沒有存取該內容的權限', 
				$statusCode
			);
		} else {
			$this->showError(
				$statusCode, 
				'禁止存取', 
				'您沒有存取內容的權限',
				$isJson
			);
		}
		
		return $this->response->withStatus($statusCode);
	}

	protected function showError($title, $message, $desc = '', $btnText = '', $redirectUrl = '')
    {
		$this->view('error', [
			'title' => $title, 
			'message' => $message,
			'desc' => $desc,
			'btnText' => !empty($btnText) ? $btnText : null,
			'redirectUrl' => !empty($redirectUrl) ? $redirectUrl : null,
		]);
	}

	protected function showJsonError($status, $errorMessage, $errorCode = 0)
	{
		$this->response->write(JSend::error([
			'status' => $status, 
			'code' => $errorCode,
			'message' => $errorMessage,
		]));
	}
	
	protected function notFound()
	{
		throw new NotFoundException($this->request, $this->response);
	}

	protected function error()
	{
		throw new ErrorException;
	}
}
