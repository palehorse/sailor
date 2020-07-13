<?php
namespace Sailor\Core\Handlers;

use Sailor\Core\Config;
use Sailor\Core\Interfaces\ErrorHandler;
use Slim\Views\Twig;

class NotFoundHandler implements ErrorHandler
{   
    /** @var mixed */
    private $name;

    /** @var Twig */
    private $twig;

    /** @var string */
    private $template;

    /** @var string */
    private $title;

    /** @var string */
    private $message;

    /** @var string */
    private $desc;

    /**
     * @param Twig $Twig
     */
    public function __construct()
    {
        $this->name = 'notFoundHandler';
        $this->template = Config::get('notfound.TEMPLATE');
        $this->title = Config::get('notfound.TITLE');
        $this->message = Config::get('notfound.MESSAGE');
        $this->desc = Config::get('notfound.DESC');
    }

    /**
     * Return the name for Container
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Twig 
     * 
     * @param Twig $twig
     */
    public function setTwig(Twig $twig) 
    {
        $this->twig = $twig;
    }

    /**
     * Build a Callable for not found
     * 
     * @return Callable
     */
    public function buildHandler()
    {
        $twig = $this->twig;
        $template = $this->template;
        $title = $this->title;
        $message = $this->message;
        $desc = $this->desc;

        return function() use ($twig, $template, $title, $message, $desc) {
            return function($request, $response) use ($twig, $template, $title, $message, $desc) {
                if (!preg_match('/\.php$/', $template)) {
                    $template .= '.php';
                }

                $response->withStatus(404);

                $twig->render(
                    $response,
                    $template, 
                    [
                        'title' => $title,
                        'message' => $message,
                        'desc' => $desc,
                    ]
                );

                return $response;
            };
        };
    }
}