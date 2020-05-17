<?php
namespace Pussle\ORM\Data;

use Pussle\ORM\Interfaces\ColumnInterface;
use Pussle\ORM\Interfaces\SQLInterface;

class Parameter implements SQLInterface
{
    /** @var ColumnInterface */
    private $column;

    /** @var string */
    private $operator;

    /** @var array */
    private $values;

    /** @var string */
    private $relation;

    /**
     * @param Table $table
     * @param ColumnInterface $column
     * @param string $operator
     * @param array $values
     */
    public function __construct(ColumnInterface $column, $operator, array $values)
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->values = $values;
    }

    /**
     * @return ColumnInterface
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $value
     */
    public function addValue($value)
    {
        $this->values[] = $value;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getValue($key=null)
    {
        if (empty($this->values)) {
            return null;
        }

        if (is_null($key)) {
            return array_shift($this->values);
        }

        if (!isset($this->values[$key])) {
            return null;
        }

        return $this->values[$key];
    }

    /**
     * @return array 
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param string $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    public function buildStatement()
    {
        return $this->column->buildStatement();
    }
}