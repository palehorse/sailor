<?php
namespace Pussle\ORM\SQL;

use Pussle\ORM\Models\Table;
use RuntimeException;

class Where implements Command
{
    /** @var array */
    private $sqlComponents = [];

    /** @var array */
    private $params = [];

    public function __construct(array $where = [])
    {
        $this->putIntoSqlComponents($where);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $column | The name of the column
     * @param mixed $value | The value of the column
     */
    public function add($column, $value, $operator = null, $command = null)
    {
        if (!empty($this->sqlComponents) && is_null($command)) {
            $command = 'AND';
        }

        if (!is_null($operator)) {
            if (!in_array($command, ['=', '>=', '<=', 'IS NULL', 'IS NOT NULL'])) {
                throw new RuntimeException('Only AND or OR is correct.');
            }
        } else {
            $operator = '=';
        }

        if (!is_null($command)) {
            if (!in_array($command, ['AND', 'OR'])) {
                throw new RuntimeException('Only AND or OR is correct.');
            }
            
            if (!empty($this->sqlComponents)) {
                $this->sqlComponents[] = $command;
            }
        }

        $this->sqlComponents[] = $column . (!in_array($operator, ['IS NULL', 'IS NOT NULL']) ? $operator . '?' : $operator);
        $this->params[] = $value;
        return $this;
    }

    /**
     * @param array $values
     * @param string $command
     */
    public function addIn($name, $values, $command = null)
    {
        if (empty($this->sqlComponents) && is_null($command)) {
            $command = 'AND';
        }

        if (!is_null($command)) {
            if (!in_array($command, ['AND', 'OR'])) {
                throw new RuntimeException('Only AND or OR is correct.');
            }
    
            if (!empty($this->sqlComponents)) {
                $this->sqlComponents[] = $command;
            }
        }
        
        $this->sqlComponents[] = $name . ' IN (' . implode(',', array_fill(0, count($values), '?')) . ')';
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    /**
     * @param string $command
     * @param Where $where
     * @param string $command
     */
    public function setWhere(Where $where, $command = null)
    {
        if (empty($this->sqlComponents) && is_null($command)) {
            $command = 'AND';
        }

        if (!is_null($command)) {
            if (!in_array($command, ['AND', 'OR'])) {
                throw new RuntimeException('Only AND or OR is correct.');
            }
    
            if (!empty($this->sqlComponents)) {
                $this->sqlComponents[] = $command;
            }
        }
        
        $this->sqlComponents[] = $where->build();
        $this->params = array_merge($this->params, $where->getParams());
        return $this;
    }

    /**
     * @param mixed $lower
     * @param mixed $upper
     * @param string | null $command
     */
    public function addBetween($name, $lower, $upper, $command = null)
    {
        if (empty($this->sqlComponents) && is_null($command)) {
            $command = 'AND';
        }

        if (!is_null($command)) {
            if (!in_array($command, ['AND', 'OR'])) {
                throw new RuntimeException('Only AND or OR is correct.');
            }
    
            if (!empty($this->sqlComponents)) {
                $this->sqlComponents[] = $command;
            }
        }

        $this->sqlComponents[] = $name . ' BETWEEN ? AND ?';
        $this->params = array_merge($this->params, [$lower, $upper]);
        return $this;
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
        return implode(' ', $this->sqlComponents);
    }

    /**
     * @param array $where
     */
    private function putIntoSqlComponents(array $where)
    {
        if (empty($where)) {
            return;
        }

        if (sizeof(array_filter($where, 'is_array')) == 0) {
            $where = [$where];
        }

        $components = [];
        foreach ($where as $row) {
            if (is_array($row)) {
                list($column, $operator, $value) = $row;
                $this->params[] = $value;
                $components[] = sprintf('%s%s?', $column, $operator);
            } else {
                if (in_array($row, ['AND', 'OR'])) {
                    $components[] = $row;
                } else {
                    $components[] = 'AND';
                }
            }
        }
        
        $this->sqlComponents = array_merge($this->sqlComponents, $components);
    }
}