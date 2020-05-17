<?php
namespace Pussle\ORM\Funcs;

use Pussle\ORM\Interfaces\ColumnInterface;
use Pussle\ORM\Interfaces\FuncInterface;
use Pussle\ORM\Traits\AliasTrait;

class Substr implements FuncInterface
{
    use AliasTrait;

    /** @var ColumnInterface */
    private $column;

    /** @var integer */
    private $start;

    /** @var integer */
    private $length;

    /** @var string */
    private $alias;

    /**
     * @param ColumnInterface $column
     * @param integer $start
     * @param integer $length
     */
    public function __construct(ColumnInterface $column, $start, $length)
    {
        $this->column = $column;
        $this->start = $start;
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'SUBSTR';
    }

    /**
     * @return ColumnInterface
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return string | the SQL statement 
     */
    public function buildStatement()
    {
        return sprintf(
            '%s(%s, %d, %d)', 
            $this->getName(),
            $this->column->buildStatement(), 
            $this->start,
            $this->length
        );
    }
}