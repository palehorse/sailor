<?php

namespace Sailor\Core\Interfaces;

Interface File {
	public static function create($path);
	public function getName();
	public function getDir();
	public function getContent();
	public function getExt();
	public function validate($path);
}