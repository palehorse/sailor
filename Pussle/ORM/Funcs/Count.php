<?php
namespace Pussle\ORM\Funcs;

use Pussle\ORM\Interfaces\FuncInterface;
use Pussle\ORM\Traits\AliasTrait;
use Pussle\ORM\Data\Column;

class Count implements FuncInterface
{
    use AliasTrait;

    /** @var Column */
    private $column;

    /** @var string */
    private $alias;

    /**
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'COUNT';
    }

    /**
     * @return Column
     */
    public function getColumn()
    {
        return $this->column;
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