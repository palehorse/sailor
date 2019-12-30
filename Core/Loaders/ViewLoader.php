<?php

namespace Sailor\Core\Loaders;

use Sailor\Core\Interfaces\Loaded;
use Sailor\Core\Interfaces\Loader;
use Sailor\Core\Services\View;
use Slim\Views\Twig;

class ViewLoader implements Loader
{
    const VIEW_PATH = __DIR__ . '/../../resources/views';

    private static $twig;
    public static function create()
    {
        return new ViewLoader;
    }

    public function load(Loaded $viewExtensionFile)
    {
        self::$twig->addExtension($viewExtensionFile->resolve());
    }

    public static function getTwig()
    {
        if (empty(self::$twig)) {
            self::$twig = new Twig(self::VIEW_PATH, []);
        }

        return self::$twig;
    }
}