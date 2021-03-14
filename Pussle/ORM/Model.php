<?php
namespace Pussle\ORM;

use Pussle\ORM\Data\Column;
use Pussle\ORM\Data\OrderColumn;
use Pussle\ORM\Data\Parameter;
use Pussle\ORM\Data\Table;
use Pussle\ORM\Funcs\Ascii;
use Pussle\ORM\Funcs\Avg;
use Pussle\ORM\Funcs\Count;
use Pussle\ORM\Funcs\GroupConcat;
use Pussle\ORM\Funcs\Max;
use Pussle\ORM\Funcs\Min;
use Pussle\ORM\Funcs\Substr;
use Pussle\ORM\Funcs\Sum;
use Pussle\ORM\Funcs\Trim;
use Pussle\ORM\Interfaces\ColumnInterface;
use Pussle\ORM\Interfaces\FuncInterface;
use Pussle\ORM\Language\Clause;
use Pussle\ORM\Language\Delete;
use Pussle\ORM\Language\Group;
use Pussle\ORM\Language\Having;
use Pussle\ORM\Language\Insert;
use Pussle\ORM\Language\Join;
use Pussle\ORM\Language\Order;
use Pussle\ORM\Language\Limit;
use Pussle\ORM\Language\On;
use Pussle\ORM\Language\Select;
use Pussle\ORM\Language\SQLStatement;
use Pussle\ORM\Language\Update;
use Pussle\Database;

class Model
{
    /** @var string */
    protected $tableName;

    /** @var Table */
    protected $table;

    /** @var SQLStatement */
    private $sqlStatement;

    public function __construct()
    {
        $this->table = new Table($this->tableName);
        $this->sqlStatement = new SQLStatement;
    }

    /**
     * @return SQLStatement
     */
    public function getSQLStatement()
    {
        return $this->sqlStatement;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $columnName
     */
    public function count($columnName, $as=null)
    {
        $select = $this->getSelect();
        $count = new Count(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $count->setAs($as);
        }
        $select->addFunc($count);
        return $count;
    }

    /**
     * @param string $columnName
     */
    public function groupConcat($columnName, $glue=null, $as=null)
    {
        $select = $this->getSelect();
        $concat = new GroupConcat(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $concat->setAs($as);
        }
        $select->addFunc($concat);
        return $concat;
    }

    /**
     * @param string $columnName
     */
    public function avg($columnName, $as=null)
    {
        $select = $this->getSelect();
        $avg = new Avg(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $avg->setAs($as);
        }
        $select->addFunc($avg);
        return $avg;
    }

    /**
     * @param string $columnName
     */
    public function sum($columnName, $as=null)
    {
        $select = $this->getSelect();
        $sum = new Sum(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $sum->setAs($as);
        }
        $select->addFunc($sum);
        return $sum;
    }

    /**
     * @param string $columnName
     */
    public function substr($columnName, $start, $length, $as=null)
    {
        $select = $this->getSelect();
        $substr = new Substr(new Column($this->table, $columnName), $start, $length, $as);
        if (!is_null($as)) {
            $substr->setAs($as);
        }
        $select->addFunc($substr);
        return $substr;
    }

    /**
     * @param string $columnName
     */
    public function max($columnName, $as=null)
    {
        $select = $this->getSelect();
        $max = new Max(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $max->setAs($as);
        }
        $select->addFunc($max);
        return $max;
    }

    /**
     * @param string $columnName
     */
    public function min($columnName, $as=null)
    {
        $select = $this->getSelect();
        $min = new Min(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $min->setAs($as);
        }
        $select->addFunc($min);
        return $min;
    }

    /**
     * @param string $columnName
     */
    public function trim($columnName, $as=null)
    {
        $select = $this->getSelect();
        $trim = new Trim(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $trim->setAs($as);
        }
        $select->addFunc($trim);
        return $trim;
    }

    /**
     * @param string $columnName
     */
    public function ascii($columnName, $as=null)
    {
        $select = $this->getSelect();
        $ascii = new Ascii(new Column($this->table, $columnName));
        if (!is_null($as)) {
            $ascii->setAs($as);
        }
        $select->addFunc($ascii);
        return $ascii;
    }

    /**
     * @param array $columns
     */
    public function select(array $columnNames=[])
    {
        $select = new Select($this->table);
        foreach ($columnNames as $columnName) {
            if (strpos($columnName, ' as ') !== false) {
                list($originColumnName, $aliasColumnName) = explode(' as ', $columnName, 2);
                $select->addColumn(new Column($this->table, $originColumnName, $aliasColumnName));
            } else {
                $select->addColumn(new Column($this->table, $columnName));
            }
        }

        $this->sqlStatement->setDML($select);
    }

    /**
     * @param array $columns
     */
    public function insert(array $columns)
    {
        $insert = $this->getInsert();
        $allParameters = $insert->getParameters();
        foreach ($columns as $name => $value) {
            $parameters = array_filter($allParameters, function($parameter) use ($name) {
                return $parameter->getColumn()->getName() == $name;
            });

            if (!empty($parameters)) {
                $parameter = array_shift($parameters);
                $parameter->addValue($value);
            } else {
                $insert->addParameter(new Parameter(new Column($this->table, $name), '=', [$value]));
            }
        }
    }

    public function join(Model $model, $on = [])
    {
        $this->baseJoin($model, $on);
    }

    public function leftJoin(Model $model, $on = [])
    {
        $this->baseJoin($model, $on, 'LEFT');
    }

    public function rightJoin(Model $model, $on = [])
    {
        $this->baseJoin($model, $on, 'RIGHT');
    }

    /**
     * @param array $columns
     */
    public function update(array $columns)
    {
        $update = new Update($this->table);
        foreach ($columns as $columnName => $columnValue) {
            $update->addParameter(new Parameter(new Column($this->table, $columnName), '=', [$columnValue]));
        }

        $this->sqlStatement->setDML($update);
    }

    public function where()
    {
        $args = func_get_args();
        $operator = in_array(strtoupper($args[1]), ['=', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? strtoupper($args[1]) : '=';
        $parameterValue = in_array(strtoupper($args[1]), ['=', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? $args[2] : $args[1];
        $clause = $this->getClause();

        if ($args[0] instanceof ColumnInterface) {
            $parameter = new Parameter($args[0], $operator, is_array($parameterValue) ? $parameterValue : [$parameterValue]);
        } else {
            $parameter = new Parameter(new Column($this->table, $args[0]), $operator, is_array($parameterValue) ? $parameterValue : [$parameterValue]);
        }

        if (!empty($clause->getParameters())) {
            $parameter->setRelation('AND');
        }
        $clause->addParameter($parameter);
    }

    /**
     * @param string $columnName
     * @param mixed $parameterValue
     */
    public function orWhere()
    {
        $args = func_get_args();
        $operator = in_array(strtoupper($args[1]), ['=', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? strtoupper($args[1]) : '=';
        $parameterValue = in_array(strtoupper($args[1]), ['=', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? $args[2] : $args[1];
        $clause = $this->getClause();
        
        if ($args[0] instanceof ColumnInterface) {
            $parameter = new Parameter($args[0], $operator, is_array($parameterValue) ? $parameterValue : [$parameterValue]);
        } else {
            $parameter = new Parameter(new Column($this->table, $args[0]), $operator, is_array($parameterValue) ? $parameterValue : [$parameterValue]);
        }

        if (!empty($clause->getParameters())) {
            $parameter->setRelation('OR');
        }
        $clause->addParameter($parameter);
    }

    /**
     * @param mixed $column
     */
    public function groupBy($column)
    {
        $group = $this->getGroup();
        if ($column instanceof ColumnInterface) {
            $group->addColumn($column);
        } else {
            $group->addColumn(new Column($this->table, $column));
        }
    }

    public function having()
    {
        $args = func_get_args();
        $operator = in_array(strtoupper($args[1]), ['=', '>', '<', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? strtoupper($args[1]) : '=';
        $parameterValue = in_array(strtoupper($args[1]), ['=', '>', '<', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? $args[2] : $args[1];
        $group = $this->sqlStatement->getGroup();

        if (empty($group)) {
            return;
        }
        
        $parameter = new Parameter(new Column($this->table, $args[0]), $operator, is_array($parameterValue) ? $parameterValue : [$parameterValue]);
        $havings = $group->getHavings();
        if (empty($havings)) {
            $having = new Having;
            $having->setRelation('AND');
            $group->addHaving($having);
            $havings = $group->getHavings();
        }

        $having = array_shift($havings);
        $having->addParameter($parameter);
    }

    public function orHaving()
    {
        $args = func_get_args();
        $operator = in_array(strtoupper($args[1]), ['=', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? strtoupper($args[1]) : '=';
        $parameterValue = in_array(strtoupper($args[1]), ['=', '<=', '>=', '!=', '<>', 'LIKE', 'BETWEEN', 'IN', 'NOT IN']) ? $args[2] : $args[1];
        $group = $this->sqlStatement->getGroup();

        if (empty($group)) {
            return;
        }
        
        $parameter = new Parameter($args[0], $operator, is_array($parameterValue) ? $parameterValue : [$parameterValue]);
        $havings = $group->getHavings();
        if (empty($havings)) {
            $having = new Having;
            $having->setRelation('OR');
            $group->addHaving($having);
            $havings = $group->getHavings();
        }

        $having = array_shift($havings);
        $having->addParameter($parameter);
    }

    /**
     * @param string $columnName
     */
    public function orderBy($column, $sort=null)
    {
        $order = $this->getOrder();
        if ($column instanceof ColumnInterface) {
            $order->addColumn(new OrderColumn($column, $sort));
        } else {
            $order->addColumn(new OrderColumn(new Column($this->table, $column), $sort));
        }
    }

    /**
     * @param string $limit
     */
    public function limit($number, $offset=null)
    {
        $this->sqlStatement->setLimit(new Limit($number, $offset));
    }
    
    public function fetch()
    {
        $sql = $this->sqlStatement;
        $stmt = Database::execute($sql);
        return $stmt->fetch();
    }

    public function fetchAll()
    {
        $sql = $this->sqlStatement;
        $stmt = Database::execute($sql);
        return $stmt->fetchAll();
    }

    public function save()
    {
        $dml = $this->sqlStatement->getDML();
        if (!empty($dml)) {
            $stmt = Database::execute($this->sqlStatement);
        }

        return $stmt->rowCount();
    }

    public function lastInsertId()
    {
        return Database::lastInsertId();
    }

    public function delete()
    {
        $this->sqlStatement->setDML(new Delete($this->table));
        $stmt = Database::execute($this->sqlStatement);
        return $stmt->rowCount();
    }

    protected function getSelect()
    {
        $select = $this->sqlStatement->getDML();

        if (empty($select) || !$select instanceof Select) {
            $this->sqlStatement->setDML(new Select($this->table));
            $select = $this->sqlStatement->getDML();
        }

        return $select;
    }

    protected function getInsert()
    {
        $insert = $this->sqlStatement->getDML();

        if (empty($insert) || !$insert instanceof Insert) {
            $this->sqlStatement->setDML(new Insert($this->table));
            $insert = $this->sqlStatement->getDML();
        }

        return $insert;
    }

    /**
     * @param Model $model
     */
    protected function baseJoin(Model $model, array $on=[], $relation = null)
    {
        $table = $model->getTable();
        $select = $this->sqlStatement->getDML();
        if (!$select instanceof Select) {
            return;
        }

        $rightSelect = $model->getSQLStatement()->getDML();
        if (!$rightSelect instanceof Select) {
            return;
        }

        $columns = $rightSelect->getColumns();
        foreach ($columns as $column) {
            $select->addColumn($column);
        }

        $funcs = $rightSelect->getFuncs();
        foreach ($funcs as $func) {
            $select->addFunc($func);
        }

        $clauses = $model->getSQLStatement()->getClauses();
        foreach ($clauses as $clause) {
            $this->sqlStatement->addClause($clause);
        }

        if (!empty($on)) {
            $isSingleArray = count(array_filter($on, function($row) {
                return is_array($row);
            })) == 0;

            $on = $isSingleArray ? [$on] : $on;

            $toOn = new On;
            foreach ($on as $row) {
                list($leftColumnName, $rightColumnName) = $row;
                $toOn->addStatement(new Column($this->table, $leftColumnName), new Column($table, $rightColumnName));
            }
        }

        $join = new Join($table);
        if (!empty($relation)) {
            $join->setRelation($relation);
        }

        $join->setOn($toOn);
        $select->addJoin($join);

        $rightGroup = $model->getSQLStatement()->getGroup();
        $group = $this->sqlStatement->getGroup();
        if (!empty($rightGroup)) {
            if (!empty($group)) {
                foreach ($rightGroup->getColumns() as $column) {
                    $group->addColumn($column);
                }
            } else {
                $this->sqlStatement->setGroup($rightGroup);
            }
        }

        $rightOrder = $model->getSQLStatement()->getOrder();
        $order = $this->sqlStatement->getOrder();
        if (!empty($rightOrder)) {
            if (!empty($order)) {
                foreach ($rightOrder->getColumns() as $column) {
                    $order->addColumn($column);
                }
            } else {
                $this->sqlStatement->setOrder($rightOrder);
            }
        }

    }

    /**
     * @return Clause | the first Clause of the SQLStatement
     */
    protected function getClause()
    {
        $clauses = $this->sqlStatement->getClauses();
        if (empty($clauses)) {
            $this->sqlStatement->addClause(new Clause);
            $clauses = $this->sqlStatement->getClauses();
        }
        
        return array_shift($clauses);
    }

    protected function getGroup()
    {
        $group = $this->sqlStatement->getGroup();
        if (empty($group)) {
            $this->sqlStatement->setGroup(new Group);
            $group = $this->sqlStatement->getGroup();
        }

        return $group;
    }

    protected function getOrder()
    {
        $order = $this->sqlStatement->getOrder();
        if (empty($order)) {
            $this->sqlStatement->setOrder(new Order);
            $order = $this->sqlStatement->getOrder();
        }

        return $order;
    }
}