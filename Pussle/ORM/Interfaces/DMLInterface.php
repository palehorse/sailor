<?php
namespace Pussle\ORM\Interfaces;

interface DMLInterface extends SQLInterface
{
    /**
     * @return Table
     */
    public function getTable();
}