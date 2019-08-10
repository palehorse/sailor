<?php

namespace Sailor\Extensions\Twig;

use \Twig\TwigFunction;
use \Twig\Extension\AbstractExtension;
use Sailor\Utility\Route;
use Sailor\Core\Route as Router;

class RouteExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('version', [$this, 'version']),
            new TwigFunction('pathFor', [$this, 'pathFor']),
        ];
    }

    public function version($url)
    {
        return Route::version($url);
    }

    public function pathFor($name)
    {
        return Router::getSlimApp()
                     ->getContainer()
                     ->get('router')
                     ->pathFor($name);
    }
}