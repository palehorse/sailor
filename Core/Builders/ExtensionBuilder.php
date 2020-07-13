<?php
namespace Sailor\Core\Builders;

class ExtensionBuilder
{
    const EXTENSION_PATH = __DIR__ . '/../../Extensions/Twig';
    
    /** @var array */
    private $files = [];

    public function __construct()
    {
        $this->files = glob(self::EXTENSION_PATH . '/*.php');
    }

    public function build()
    {
        return array_map(function($file) {
            $className = str_replace('.php', '', basename($file));
            $class = 'Sailor\\Extensions\\Twig\\' . $className; 
            return new $class;
        }, $this->files);
    }
}