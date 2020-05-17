<?php
namespace Pussle\ORM\Funcs;

use Pussle\ORM\Interfaces\ColumnInterface;
use Pussle\ORM\Interfaces\FuncInterface;
use Pussle\ORM\Traits\AliasTrait;

class Trim implements FuncInterface
{
    use AliasTrait;

    /** @var ColumnInterface */
    private $column;

    /** @var string */
    private $alias;

    public function __construct(ColumnInterface $column)
    {
        $this->column = $column;
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
    public function getName()
    {
        return 'TRIM';
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        return sprintf(
            '%s(%s)', 
            $this->getName(),
            $this->column->buildStatement()
        );
    }
}