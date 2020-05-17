<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Column;
use Pussle\ORM\Data\Table;
use Pussle\ORM\Interfaces\DMLInterface;
use Pussle\ORM\Interfaces\FuncInterface;
use Pussle\ORM\Traits\DMLTrait;

class Select implements DMLInterface
{
    use DMLTrait;
    
    /** @var Table */
    private $table;

    /** @var array */
    private $columns = [];

    /** @var array */
    private $funcs = [];

    /** @var array */
    private $joins = [];

    /**
     * @param Table | the Table manipulated by Select
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /** 
     * @param Column $column
     */
    public function addColumn(Column $column)
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
     * @param Func $func
     */
    public function addFunc(FuncInterface $func)
    {
        $this->funcs[] = $func;
    }

    /**
     * @return array
     */
    public function getFuncs()
    {
        return $this->funcs;
    }

    /**
     * @param Join $join
     */
    public function addJoin(Join $join)
    {
        $this->joins[] = $join;
    }

    /**
     * @return array
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        $fields = array_merge(
            array_map(function($column) {
                return $column->buildStatement() . (!empty($column->getAs()) ? ' AS `' . $column->getAs() . '`' : '');
            }, $this->columns),
            array_map(function($func) {
                return $func->buildStatement() . (!empty($func->getAs()) ? ' AS `' . $func->getAs() . '`' : '');
            }, $this->funcs)
        );

        return sprintf(
            'SELECT %s FROM %s%s', 
            !empty($fields) ? implode(', ', $fields) : '*',
            $this->table->buildStatement(),
            !empty($this->joins) ? implode('', array_map(function($join) {
                return $join->buildStatement();
            }, $this->joins)) : ''
        );
    }
}