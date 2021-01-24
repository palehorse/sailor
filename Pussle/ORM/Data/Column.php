<?php
namespace Pussle\ORM\Data;

use Pussle\ORM\Interfaces\ColumnInterface;
use Pussle\ORM\Traits\AliasTrait;
use Pussle\ORM\Traits\TableColumnTrait;

class Column implements ColumnInterface
{
    use AliasTrait, TableColumnTrait;

    /** @var Table */
    protected $table;

    /** @var string */
    protected $name;

    /** @var string */
    protected $alias;

    public function __construct(Table $table, $name, $alias='')
    {
        $this->table = $table;
        $this->name = $name;
        $this->alias = $alias;
    }

    public function buildStatement()
    {
        return $this->table->buildStatement() . '.`' . $this->name . '`';
    }
}