<?php
namespace Pussle\ORM\Interfaces;

interface ColumnInterface extends SQLInterface
{
    const ESCAPE_STRING = true;

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $alias 
     */
    public function setAs($alias);

    /**
     * @return string
     */
    public function getAs();
}