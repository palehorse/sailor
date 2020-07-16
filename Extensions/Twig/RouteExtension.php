<?php

namespace Sailor\Extensions\Twig;

use \Twig\TwigFunction;
use \Twig\Extension\AbstractExtension;
use Sailor\Core\Router;

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
        return Router::version($url);
    }

    public function pathFor($name)
    {
        return Router::pathFor($name);
    }
}