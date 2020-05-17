<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\OrderColumn;
use Pussle\ORM\Interfaces\SQLInterface;

class Order implements SQLInterface
{
    /** @var array */
    private $columns = [];

    /**
     * @param OrderColumn $column
     */
    public function addColumn(OrderColumn $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    public function buildStatement()
    {
        return sprintf('%s %s', 'ORDER BY', implode(', ', array_map(function($column) {
            return $column->buildStatement();
        }, $this->columns)));
    }
}