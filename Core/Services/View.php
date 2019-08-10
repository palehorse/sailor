<?php

namespace Sailor\Core\Services;

use Slim\Views\Twig;

class View 
{
    private static $twig;
    private static $viewPath = __DIR__ . '/../../resources/views';
    public static function make()
    {
        self::$twig = new Twig(self::$viewPath, []);
        self::addTwigExtensions();
    }

    public static function create()
    {
        if (empty(self::$twig)) {
            self::make();
        }
        return self::$twig;
    }

    private static function addTwigExtensions()
    {
        $namespace = 'Sailor\\Extensions\\Twig\\';
        $extensionPath = __DIR__ . '/../../Extensions/Twig/';
        $extensions = glob($extensionPath . '{*.php}', GLOB_BRACE);
        foreach ($extensions as $ext) {
            $class = $namespace . basename($ext, '.php');
            self::$twig->addExtension(new $class);
        }
    }
}