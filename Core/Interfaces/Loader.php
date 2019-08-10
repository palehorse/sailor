<?php

namespace Sailor\Core\Interfaces;

Interface Loader
{
	public static function create();
	public function resolve();
}