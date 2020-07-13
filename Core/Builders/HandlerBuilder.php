<?php
namespace Sailor\Core\Builders;

use Slim\Views\Twig;

class HandlerBuilder
{
    const HANDLERS_PATH = __DIR__ . '/../Handlers';

    /** @var array */
    private $files = [];

    /** @var Twig */
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->files = glob(self::HANDLERS_PATH . '/*.php');
        $this->twig = $twig;
    }

    public function build()
    {
        return array_map(function($file) {
            $class = "Sailor\\Core\\Handlers\\" . str_replace('.php', '', basename($file));
            $handler = new $class;
            $handler->setTwig($this->twig);
            return $handler;
        }, $this->files);
    }
}