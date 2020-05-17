<?php

namespace Sailor\Core\Loaders;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use Sailor\Core\Interfaces\Loaded;
use Sailor\Core\Interfaces\Loader;
use \RuntimeException;

class HookLoader implements Loader 
{
	/** @param HookFile */
	private $hookFile;

	/** @param callable */
	private $func;

	/** @param ReflectionFunction */
	private $reflectionFunction;

	/** @param array | Array of ReflectionParameter */
	private $reflectionParameters;

	public static function create()
	{
		return new HookLoader;
	}

	public function load(Loaded $hookFile)
	{
		$this->hookFile = $hookFile;
		$this->func = $hookFile->resolve();
		$this->reflectionFunction = new ReflectionFunction($this->func);
		$this->reflectionParameters = $this->reflectionFunction->getParameters();
	}

	public function call(&$data)
	{
		$parameters = [&$data];
		$reflectionParameters = array_slice($this->reflectionParameters, 1);
		foreach ($reflectionParameters as $parameter) {
			$parameters[] = $this->findParameters($parameter);
		}

		return call_user_func_array($this->func, $parameters);
	}

	private function findParameters(ReflectionParameter $parameter)
	{
		if (!is_null($parameter->getClass())) {
			$class = $parameter->getClass()->getName();
			$requiredVars = [];
			if (method_exists($class, '__construct')) {
				$classParameters = (new ReflectionMethod($class, '__construct'))->getParameters();
				foreach ($classParameters as $p) {
					$requiredVars[] = $this->findParameters($p);
				}
			}

			return (new \ReflectionClass($class))->newInstanceArgs($requiredVars);
		}

		throw new RuntimeException('The type of Parameter ' . $parameter->getName() . ' is incorrect.');
	}
}