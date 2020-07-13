<?php
namespace Sailor\Core\Handlers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sailor\Core\Config;
use Sailor\Core\Interfaces\ErrorHandler;
use Sailor\Core\Logger;
use Slim\Http\Request;
use Slim\Views\Twig;

class PhpErrorHandler implements ErrorHandler
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
        $this->name = ['errorHandler', 'phpErrorHandler'];
        $this->template = Config::get('error.TEMPLATE');
        $this->title = Config::get('error.TITLE');
        $this->message = Config::get('error.MESSAGE');
        $this->desc = Config::get('error.DESC');
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
            return function(
                RequestInterface $request, 
                ResponseInterface $response, 
                \Exception $exception
            ) use ($twig, $template, $title, $message, $desc) {
                if (!preg_match('/\.php$/', $template)) {
                    $template .= '.php';
                }

                $response->withStatus(500);
                Logger::error($exception->getMessage());

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