<?php
namespace Sailor\Core\Builders;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Sailor\Core\Controller;

class ControllerBuilder
{
    const CONTROLLER_NAMESPACE = 'Sailor\\Controllers';

    /** @var string */
    private $className;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /** @var Twig */
    private $twig;

    /**
     * @param string $className
     */
    public function __construct(
        $className, 
        Request $request, 
        Response $response, 
        Twig $twig
    )
    {
        $this->className = $className;
        $this->request = $request;
        $this->response = $response;
        $this->twig = $twig;

        
    }

    /**
     * @return Controller
     */
    public function build()
    {
        $class = self::CONTROLLER_NAMESPACE . '\\' . $this->className;
        return (new \ReflectionClass($class))->newInstanceArgs([
            $this->request,
            $this->response,
            $this->twig
        ]);
    }
}