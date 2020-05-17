<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Interfaces\SQLInterface;

class SQLStatement implements SQLInterface
{
    /** @var SQLInterface */
    private $dml;

    /** @var array */
    private $clauses = [];

    /** @var Group */
    private $group;

    /** @var Order */
    private $order;

    /** @var Limit */
    private $limit;

    /**
     * @param SQLInterface $dml
     */
    public function setDML(SQLInterface $dml)
    {
        $this->dml = $dml;
    }

    /**
     * @return SQLInterface
     */
    public function getDML()
    {
        return $this->dml;
    }

    /**
     * @param Clause $clause
     */
    public function addClause(Clause $clause)
    {
        $this->clauses[] = $clause;
    }

    /**
     * @return array
     */
    public function getClauses()
    {
        return $this->clauses;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Limit $limit
     */
    public function setLimit(Limit $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return Limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return Group
     */
    public function getGroup() 
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return string | the SQL statement
     */
    public function buildStatement()
    {
        return sprintf(
            '%s %s%s%s%s', 
            $this->dml->buildStatement(), 
            !empty($this->clauses) ? ' WHERE ' . implode(' ', array_map(function($clause) {
                return $clause->buildStatement();
            }, $this->clauses)) : '',
            !empty($this->group) ? ' ' . $this->group->buildStatement() : '',
            !empty($this->order) ? ' ' . $this->order->buildStatement() : '',
            !empty($this->limit) ? ' ' . $this->limit->buildStatement() : ''
        );
    }
}