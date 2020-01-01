<?php
namespace Pussle\ORM;

use PDOStatement;
use Pussle\ORM\DB\AccessResult;
use Pussle\ORM\Models\Table;
use Pussle\ORM\DB\Database;
use Pussle\ORM\SQL\Command;
use Pussle\ORM\SQL\Delete;
use Pussle\ORM\SQL\Insert;
use Pussle\ORM\SQL\Join;
use Pussle\ORM\SQL\Select;
use Pussle\ORM\SQL\Update;
use Pussle\ORM\SQL\Where;
use RuntimeException;

class Model
{
    /** @var string */
    protected $tableName;

    /** @var Table */
    protected $table;

    /** @var Command */
    protected $command;

    /** @var Model */
    protected $rightModel;

    /** @var array */
    protected $count = [];

    /** @var Where */
    protected $where;

    /** @var array */
    protected $columns = [];

    /** @var array */
    protected $group = [];

    /** @var array */
    protected $order = [];

    /** @var PDOStatement */
    protected $stmt;

    public function __construct()
    {
        if (empty($this->tableName)) {
            throw new RuntimeException('Missing the name of the table!');
        }
        $this->table = new Table($this->tableName);
        $this->getColumnsFromTable();
    }

    public function __get($name)
    {
        return isset($this->columns[$name]) ? $this->columns[$name] : null;
    }

    public function __set($name, $value)
    {
        if (in_array($name, array_keys($this->columns))) {
            $this->columns[$name] = $value;
            $this->table->addColumn($name);
            $this->table->setColumn($name, $value);
        }
    }

    /**
     * @param string $alias
     * @return Table
     */
    public function alias($alias)
    {
        if (method_exists($this->table, 'setAlias')) {
            $this->table->setAlias($alias);
        }
        return $this;
    }

    /**
     * @param string $field
     * @return Table
     */
    public function group($field)
    {
        $this->group[] = $field;
        return $this;
    }

    /**
     * @param string $field
     * @return Table
     */
    public function order($field)
    {
        $this->order[] = $field;
    }

    /**
     * @return array 
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param array $fields
     * @return Model
     */
    public function select(array $fields = [])
    {
        $columns = [];
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (in_array($field, array_keys($this->columns))) {
                    $columns[] = $field;
                }
            }
        }

        if (!empty($columns)) {
            foreach (array_keys($this->columns) as $column) {
                if (!in_array($column, $columns)) {
                    unset($this->columns[$column]);
                    $this->table->removeColumn($column);
                }
            }
        }

        $this->setCommand($this->createSelect());
        return $this;
    }

    /**
     * @param string $field
     * @return Table
     */
    public function count($field)
    {
        $this->count[] = $field;
        return $this;
    }

    public function insert()
    {
        $this->setCommand($this->createInsert());
        Database::run($this->command);
        return Database::getLastInsertId();
    }

    /**
     * @return Model
     */
    public function save()
    {
        $this->setCommand($this->createUpdate());
        if (!is_null($this->where)) {
            $this->command->setWhere($this->where);
        }

        $result = Database::run($this->command);
        $this->stmt = $result;
        return $this;
    }

    /**
     * @return Model
     */
    public function delete()
    {
        $this->setCommand($this->createDelete());
        if (!is_null($this->where)) {
            $this->command->setWhere($this->where);
        }

        $result = Database::run($this->command);
        $this->stmt = $result;
        return $this;
    }

    /**
     * @param Model $rightModel
     * @param array $on
     * @return Model
     */
    public function join(Model $rightModel, array $on, array $where = [])
    {
        $this->rightModel = $rightModel;
        $leftTable = $this->table;
        $rightTable = $this->rightModel->getTable();
        $count = array_merge(
            array_map(function($column) use ($leftTable) {
                if (!empty($leftTable->getAlias())) {
                    $name = $leftTable->getAlias();
                } else {
                    $name = $leftTable->getName();
                }
                return sprintf('%s.%s', $name, $column);
            }, $this->getCount()), 
            array_map(function($column) use ($rightTable) {
                if (!empty($rightTable->getAlias())) {
                    $name = $rightTable->getAlias();
                } else {
                    $name = $rightTable->getName();
                }
                return sprintf('%s.%s', $name, $column);
            }, $rightModel->getCount()));

        $this->setCommand($this->createJoin('JOIN', $rightModel->getTable(), $on, $count, $where));
        return $this;
    }

    /**
     * @param Model $rightModel
     * @param array $on
     * @return Model
     */
    public function leftJoin(Model $rightModel, array $on, array $where = [])
    {
        $this->rightModel = $rightModel;
        $leftTable = $this->table;
        $rightTable = $this->rightModel->getTable();
        $count = array_merge(
            array_map(function($column) use ($leftTable) {
                return $leftTable->getAlias() . '.' . $column;
            }, $this->getCount()), 
            array_map(function($column) use ($rightTable) {
                return $rightTable->getAlias() . '.' . $column;
            }, $rightModel->getCount()));

        $this->setCommand($this->createJoin('LEFT JOIN', $rightModel->getTable(), $count, $on, $where));
        return $this;
    }

    /**
     * @param Model $rightModel
     * @param array $on
     * @return Model
     */
    public function rightJoin(Model $rightModel, array $on, array $where = [])
    {
        $this->rightModel = $rightModel;
        $leftTable = $this->table;
        $rightTable = $this->rightModel->getTable();
        $count = array_merge(
            array_map(function($column) use ($leftTable) {
                return $leftTable->getAlias() . '.' . $column;
            }, $this->getCount()), 
            array_map(function($column) use ($rightTable) {
                return $rightTable->getAlias() . '.' . $column;
            }, $rightModel->getCount()));

        $this->setCommand($this->createJoin('RIGHT JOIN', $rightModel->getTable(), $count, $on, $where));
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string|null
     * @return Model
     */
    public function where($name, $value, $operator = null)
    {
        if (empty($this->where)) {
            $this->where = new Where;
        }

        $this->where->add($name, $value, $operator);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string|null
     */
    public function andWhere($name, $value, $operator = null)
    {
        if (empty($this->where)) {
            $this->where = new Where;
        }

        $this->where->add($name, $value, $operator, 'AND');
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string|null
     */
    public function orWhere($name, $value, $operator = null)
    {
        if (empty($this->where)) {
            $this->where = new Where;
        }

        $this->where->add($name, $value, $operator, 'OR');
        return $this;
    }

    public function in($name, array $values, $command = null)
    {
        if (empty($this->where)) {
            $this->where = new Where;
        }

        $this->where->addIn($name, $values, $command);
        return $this;
    }

    public function between($name, $lower, $upper, $command = null)
    {
        if (empty($this->where)) {
            $this->where = new Where;
        }

        $this->where->addBetween($name, $lower, $upper, $command);
        return $this;
    }

    public function includeWhere(Where $where, $command = null)
    {
        if (empty($this->where)) {
            $this->where = new Where;
        }

        $this->where->setWhere($where, $command);
        return $this;
    }

    public function getWhere()
    {
        return $this->where;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function fetch()
    {
        if (!is_null($this->where)) {
            $this->command->setWhere($this->where);
        }
        $result = Database::run($this->command);
        $this->stmt = $result;
        $data = $result->fetch();
        if (empty($data)) {
            return null;
        }

        $Model = $this->createSelf();
        if (!$this->command instanceof Join) {
            foreach ($data as $name => $value) {
                $Model->$name = $value;
                $Model->getTable()->addColumn($name);
                $Model->getTable()->setColumn($name, $value);
            }

            return $Model;
        }
        return $data;
    }

    public function fetchAll()
    {
        if (!is_null($this->where)) {
            $this->command->setWhere($this->where);
        }

        $result = Database::run($this->command);
        $this->stmt = $result;
        $data = $result->fetchAll();
        $Models = [];
        if (!$this->command instanceof Join) {
            foreach ($data as $row) {
                $Model = $this->createSelf();
                foreach ($row as $name => $value) {
                    $Model->$name = $value;
                    $Model->getTable()->setColumn($name, $value);
                }
                $Models[] = $Model;
            }
            return $Models;
        }
        return $data;
    }

    public function rowCount()
    {
        if ($this->stmt instanceof AccessResult) {
            return $this->stmt->rowCount();
        }
        return false;
    }

    public function beginTransaction()
    {
        return Database::beginTransaction();
    }

    public function commit()
    {
        return Database::commit();
    }

    protected function setCommand(Command $command)
    {
        $this->command = $command;
    }

    protected function createSelect()
    {
        return new Select($this->table, $this->count, $this->group, $this->order);
    }

    protected function createInsert()
    {
        foreach ($this->columns as $name => $value) {
            $this->table->setColumn($name, $value);
        }
        return new Insert($this->table);
    }

    protected function createUpdate()
    {
        foreach ($this->columns as $name => $value) {
            $this->table->setColumn($name, $value);
        }
        return new Update($this->table);
    }

    protected function createDelete()
    {
        return new Delete($this->table);
    }

    protected function createJoin($type, Table $rightTable, array $count, array $on, array $where = [])
    {
        $leftTable = $this->table;
        return new Join($type, $leftTable, $rightTable, $count, $on, $where);
    }

    protected function createSelf()
    {
        $className = get_class($this);
        return new $className;
    }

    protected function getColumnsFromTable()
    {
        $sql = "SHOW COLUMNS FROM `" . $this->tableName . '`';
        $result = Database::execute($sql);
        $columns = $result->fetchAll();
        foreach ($columns as $column) {
            $name = $column['Field'];
            $this->columns[$name] = null;
            $this->table->addColumn($name);
        }
    }
}