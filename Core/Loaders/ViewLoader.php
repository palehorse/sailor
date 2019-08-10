<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loader;
use Sailor\Core\Services\View;

class ViewLoader implements Loader
{
    private $twig;
    public static function create()
    {
        return new ViewLoader;
    }

    public function __construct()
    {
        $this->twig = View::create();
    }

    public function resolve()
    {
        return $this->twig;
    }
}