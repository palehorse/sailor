<?php
namespace Pussle\ORM\Traits;

trait TableColumnTrait
{
    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}