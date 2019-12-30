<?php

namespace Sailor\Core\Interfaces;

Interface File extends Loaded {
	public static function create();
	public function getName();
	public function getBaseName();
	public function getDir();
	public function getExt();
}