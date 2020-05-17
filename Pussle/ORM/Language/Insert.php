<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Parameter;
use Pussle\ORM\Data\Table;
use Pussle\ORM\Interfaces\DMLInterface;
use Pussle\ORM\Traits\DMLTrait;

class Insert implements DMLInterface
{
    use DMLTrait;

    /** @var Table */
    private $table;

    /** @var array */
    private $parameters = [];

    /**
     * @param Table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @param Parameter $parameter
     */
    public function addParameter(Parameter $parameter)
    {
        $this->parameters[] = $parameter;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        return sprintf(
            '%s %s (%s) VALUES %s',
            'INSERT INTO',
            $this->table->buildStatement(),
            implode(', ', array_map(function($parameter) {
                return '`' . $parameter->getColumn()->getName() . '`';
            }, $this->parameters)),
            implode(
                ', ', 
                array_fill(
                    0, 
                    !empty($this->parameters) ? count($this->parameters[0]->getValues()) : 0, 
                    '(' . implode(
                        ',', 
                        array_fill(
                            0, 
                            !empty($this->parameters) ? count($this->parameters) : 0, 
                            '?'
                        )
                    ) . ')'
                )
            )
        );
    }
}