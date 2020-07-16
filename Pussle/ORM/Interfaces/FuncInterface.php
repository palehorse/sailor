<?php
namespace Pussle\ORM\Interfaces;

interface FuncInterface extends ColumnInterface
{
    /**
     * @return Column
     */
    public function getColumn();
}