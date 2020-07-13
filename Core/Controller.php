<?php

namespace Sailor\Core;

use ErrorException;
use Sailor\Core\Loaders\HookLoader;
use Sailor\Utility\JSend;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class Controller
{
	/** @var Request */
	protected $request;

	/** @var Response */
	protected $response;

	/** @var Twig */
	protected $twig;

	/** @var array */
	protected $hookLoaders = [];

	/** @var array */
	protected $data = [];

	
	protected $getVars;
	protected $postVars;
	protected $logger;

	public static function getNameSpace()
	{
		return __NAMESPACE__;
	}

	public function __construct(Request $request, Response $response, Twig $twig)
	{
		$this->request = $request;
		$this->response = $response;
		$this->twig = $twig;
		$this->getVars = $this->request->getQueryParams();
		$this->postVars = $this->request->getParsedBody();
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

		return $this->twig->render($this->response, $file, array_merge($this->data, $data));
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

	protected function forbidden($isJson = false)
	{
		$statusCode = 403;

		if ($isJson === true) {
			$this->showJsonError(
				'forbidden', 
				'您沒有存取該內容的權限<br />請確認您所使用的驗證資訊是否正確', 
				$statusCode
			);
		} else {
			$this->showError(
				$statusCode, 
				'禁止存取', 
				'您沒有存取該內容的權限<br />請確認您所使用的驗證資訊是否正確',
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
		$this->response->withJson(JSend::error([
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
