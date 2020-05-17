<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Column;
use Pussle\ORM\Data\GroupColumn;
use Pussle\ORM\Data\Table;
use Pussle\ORM\Interfaces\ColumnInterface;
use Pussle\ORM\Interfaces\SQLInterface;

class Group implements SQLInterface
{
    /** @var array */
    private $havings = [];
    
    /**
     * @param GroupColumn
     */
    public function addColumn(ColumnInterface $column)
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

    /**
     * @param Having
     */
    public function addHaving(Having $having)
    {
        $this->havings[] = $having;
    }

    /**
     * @return Having
     */
    public function getHavings()
    {
        return $this->havings;
    }

    /**
     * @return string | the SQL statement
     */
    public function buildStatement()
    {
        return sprintf(
            '%s %s%s', 
            'GROUP BY', 
            implode(', ', array_map(function($column) {
                return !empty($column->getAs()) ? '`' . $column->getAs() . '`' : $column->buildStatement();
            }, $this->columns)),
            !empty($this->havings) ? ' HAVING ' . implode(' ', array_map(function($having) {
                return $having->buildStatement();
            }, $this->havings)) : ''
        );
    }
}