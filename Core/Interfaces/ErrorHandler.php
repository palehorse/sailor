<?php
namespace Sailor\Core\Interfaces;

use Slim\Views\Twig;

interface ErrorHandler
{
    /**
     * Return the name for Container
     * 
     * @return string
     */
    public function getName();

    /**
     * Set Twig
     * 
     * @param Twig $twig
     */
    public function setTwig(Twig $twig);

    /** 
     * Build a handler for errors
     * 
     * @return Callable
     */
    public function buildHandler();
}