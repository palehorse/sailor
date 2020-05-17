<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Table;
use Pussle\ORM\Interfaces\DMLInterface;
use Pussle\ORM\Traits\DMLTrait;

class Join implements DMLInterface
{
    use DMLTrait;

    /** @var Table */
    private $table;

    /** @var string */
    private $relation;

    /** @var On */
    private $on;

    /**
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @param string $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @param On $on
     */
    public function setOn(On $on)
    {
        $this->on = $on;
    }

    /**
     * @return On
     */
    public function getOn()
    {
        return $this->on;
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        switch ($this->relation) {
            default:
                $relation = 'JOIN';
                break;
            case 'LEFT':
                $relation = 'LEFT JOIN';
                break;
            case 'RIGHT':
                $relation = 'RIGHT JOIN';
                break;
            case 'INNER';
                $relation = 'INNER JOIN';
                break;
            case 'OUTER';
                $relation = 'OUTER JOIN';
                break;
        }

        return sprintf(
            ' %s %s%s%s',
            $relation,
            '`' . $this->table->getName() . '`',
            !empty($this->table->getAs()) ? ' AS `' . $this->table->getAs() . '`' : '',
            !empty($this->on) ? ' ON ' . $this->on->buildStatement() : ''
        );
    }
}