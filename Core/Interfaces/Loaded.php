<?php
namespace Sailor\Core\Interfaces;

interface Loaded
{
    public static function create();
    public function resolve();
}