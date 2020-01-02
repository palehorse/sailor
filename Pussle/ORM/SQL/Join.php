<?php

namespace Pussle\ORM\SQL;

use Pussle\ORM\Models\Table;
use RuntimeException;

class Join implements Command
{
    /** @var Table */
    private $leftTable;

    /** @var Table */
    private $rightTable;

    /** @var string */
    private $type;

    /** @var array */
    private $on = [];

    /** @var array */
    private $count = [];

    /** @var array */
    private $where = [];

    /** @var array */
    private $group = [];

    /** @var array */
    private $order = [];

    public function __construct($type, Table $leftTable, Table $rightTable, array $on, array $count = [], array $where = [], array $group = [], array $order = [])
    {
        if (!in_array($type, ['JOIN', 'LEFT JOIN', 'RIGHT JOIN'])) {
            throw new RuntimeException($type . ' is invalid');
        }
 
        $this->type = $type;
        $this->leftTable = $leftTable;
        $this->rightTable = $rightTable;
        $this->count = $count;
        $this->on = (sizeof(array_filter($on, 'is_array')) > 0) ? $on : [$on];
        $this->where = new Where($where);
        $this->group = $group;
        $this->order = $order;
    }

    /**
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getLeftTable()
    {
        return $this->leftTable;
    }

    /**
     * @return string
     */
    public function getRightTable()
    {
        return $this->rightTable;
    }

    /**
     * @return array
     */
    public function getOn()
    {
        return $this->on;
    }
    
    /**
     * @param Where $where
     */
    public function setWhere(Where $where)
    {
        $this->where = $where;
    }

    /**
     * @return Where
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return string | The SQL statement
     */
    public function build()
    {
        $leftTable = $this->leftTable;
        $rightTable = $this->rightTable;

        $leftTableColumnNames = array_map(function($column) use ($leftTable) {
            $name = !empty($leftTable->getAlias()) ? $leftTable->getAlias() : $leftTable->getName();
            return sprintf('%s.%s', $name, $column);
        }, $leftTable->getColumnNames());

        $rightTableColumnNames = array_map(function($column) use ($rightTable) {
            $name = !empty($rightTable->getAlias()) ? $rightTable->getAlias() : $rightTable->getName();
            return sprintf('%s.%s', $name, $column);
        }, $rightTable->getColumnNames());

        $on = array_map(function($columns) use ($leftTable, $rightTable) {
            $leftTableName = !empty($leftTable->getAlias()) ? $leftTable->getAlias() : $leftTable->getName();
            $rightTableName = !empty($rightTable->getAlias()) ? $rightTable->getAlias() : $rightTable->getName();

            list($leftColumn, $rightColumn) = $columns;
            return sprintf('%s=%s', $leftTableName . '.' . $leftColumn, $rightTableName . '.' . $rightColumn);
        }, $this->on);

        $count = array_map(function($field) {
            if (is_array($field) && isset($field['as'])) {
                return sprintf('COUNT(%s) AS %s', $field[0], $field['as']);
            }
            return 'COUNT(' . $field . ')';
        }, $this->count);

        $sqlComponents = [
            'SELECT',
            implode(',', $leftTableColumnNames) . ',' . implode(',', $rightTableColumnNames) . (!empty($count) ? ',' . implode(',', $count) : ''),
            'FROM',
            '`' . $leftTable->getName() . '`',
            !empty($leftTable->getAlias()) ? 'AS ' . $leftTable->getAlias() : '',
            $this->type,
            '`' . $rightTable->getName() . '`',
            !empty($rightTable->getAlias()) ? 'AS ' . $rightTable->getAlias() : '',
            'ON',
            implode('AND', $on),
        ];

        if (!empty($this->where)) {
            $sqlComponents[] = 'WHERE';
            $sqlComponents[] = $this->where->build();
        }

        if (!empty($this->group)) {
            $sqlComponents[] = 'GROUP BY';
            $sqlComponents[] = implode(',', $this->group);
        }

        if (!empty($this->order)) {
            $sqlComponents[] = 'ORDER BY';
            $order = $this->order;
            $sequence = array_pop($order);
            if (!in_array($sequence, ['ASC', 'DESC'])) {
                $sqlComponents[] = implode(',', $this->order);
                $sqlComponents[] = 'ASC';
            } else {
                $sqlComponents[] = implode(',', $order);
                $sqlComponents[] = $sequence;
            }
        }

        return implode(' ', $sqlComponents);
    }
}