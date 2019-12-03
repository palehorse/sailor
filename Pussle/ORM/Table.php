<?php

namespace Pussle\ORM;

use \PDO;
use Sailor\Core\LoggerFactory as Logger;
use Sailor\Utility\Arr;
use Pussle\Processors\DatabaseProcessor;
use Pussle\Processors\SQLProcessor;



class Table
{
    protected $table;
    protected $countField;
    protected $columns = [];
    protected $bind = [];
    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        DatabaseProcessor::init($config);
        SQLProcessor::setTable($this->table);
        $this->fetchColumns();
    }

    public function __get($name) 
    {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }
        return null;
    }

    public function __set($name, $value) 
    {
        if (isset($this->columns[$name])) {
            $this->columns[$name] = $this->bind[$name] = $value;
            SQLProcessor::addFields($name);
            $this->bind[$name] = $value;
        }
    }

    public function beginTransaction()
    {
        return DatabaseProcessor::beginTransaction();
    }

    public function commit()
    {
        return DatabaseProcessor::commit();
    }

    public function rollback()
    {
        return DatabaseProcessor::rollback();
    }

    protected function fetchColumns()
    {
        $sql = "SELECT COLUMN_NAME FROM Information_Schema.COLUMNS WHERE TABLE_NAME='$this->table'";
        DatabaseProcessor::execute($sql);
        $columns = DatabaseProcessor::fetchAll(DatabaseProcessor::FETCH_ASSOC);
        foreach ($columns as $row) {
            $this->columns[$row['COLUMN_NAME']] = '';
        }
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function select($fields)
    {
        if (Arr::isSingleDimension($fields)) {
            SQLProcessor::addFields($fields);
        }

        if (is_string($fields)) {
            if ($fields == '*') {
                SQLProcessor::addFields(array_keys($this->columns));
            } else {
                SQLProcessor::addFields($fields);
            }
        }

        SQLProcessor::setCommand('SELECT');
        
        return $this;
    }

    public function where($clause, $value=NULL)
    {
        $gate = '&';
        $equations = [];
        $pattern = '/^([a-zA-Z_]+)([\<\>=]{1,2})$/';
        if (is_string($clause) && !is_null($value)) {
            preg_match($pattern, $clause, $matches);
            if (!empty($matches)) {
                $equations[] = [$matches[1], $matches[2], $matches[1]];
                $this->bind[$matches[1]] = $value;
            } else {
                $equations[] = [$clause, '=', $clause];
                $this->bind[$clause] = $value;
            }
        }
        
        if (is_array($clause) && !Arr::isSingleDimension($clause)) {
            foreach (array_shift($clause) as $key => $value) {
                preg_match($pattern, $key, $matches);
                if (!empty($matches)) {
                    $equations[] = [$matches[1], $matches[2], $matches[1]];
                    $this->bind[$matches[1]] = $value;
                } else {
                    $equations[] = [$key, '=', $key];
                    $this->bind[$key] = $value;
                }
            }
            $gate = array_shift($clause);
        }

        SQLProcessor::pushAND([$equations, $gate]);
        return $this;
    }

    public function andWith($clause, $value=NULL)
    {
        return $this->where($clause, $value);
    }

    public function orWith($clause, $value=NULL)
    {
        $gate = '&';
        $equations = [];
        $pattern = '/^([a-zA-Z_]+)([\<\>=]{1,2})$/';
        if (is_string($clause) && !is_null($value)) {
            preg_match($pattern, $clause, $matches);
            if (!empty($matches)) {
                $equations[] = [$matches[1], $matches[2], $matches[1]];
                $this->bind[$matches[1]] = $value;
            } else {
                $equations[] = [$clause, '=', $clause];
                $this->bind[$clause] = $value;
            }
        }
        
        if (is_array($clause) && !Arr::isSingleDimension($clause)) {
            foreach (array_shift($clause) as $key => $value) {
                preg_match($pattern, $key, $matches);
                if (!empty($matches)) {
                    $equations[] = [$matches[1], $matches[2], $matches[1]];
                    $this->bind[$matches[1]] = $value;
                } else {
                    $equations[] = [$key, '=', $key];
                    $this->bind[$key] = $value;
                }
            }
            $gate = array_shift($clause);
        }

        SQLProcessor::pushOR([$equations, $gate]);
        return $this;
    }

    public function andBetween($clause, $value=NULL)
    {
        $gate = '&';
        $equations = [];
        if (is_string($clause) && is_string($value)) {
            list($start, $end) = explode(',', preg_replace('/ /', '', $value));
            $equations[] = [$clause, '>=', 'start_' . $clause];
            $equations[] = [$clause, '<=', 'end_' . $clause];
            $this->bind['start_' . $clause] = $start;
            $this->bind['end_' . $clause]   = $end;
        }

        if (is_array($clause) && !Arr::isSingleDimension($clause)) {
            foreach (array_shift($clause) as $key => $value) {
                list($start, $end) = explode(',', preg_replace('/ /', '', $value));
                $equations[] = [$key, '>=', 'start_' . $key];
                $equations[] = [$key, '<=', 'end_' . $key];
                $this->bind['start_' . $key] = $start;
                $this->bind['end_' . $key]   = $end;
            }
            $gate = array_shift($clause);
        }

        SQLProcessor::pushAND([$equations, $gate]);
        return $this;
    }

    public function orBetween($clause, $value=NULL)
    {
        $gate = '&';
        $equations = [];
        if (is_string($clause) && is_string($value)) {
            list($start, $end) = explode(',', preg_replace('/ /', '', $value));
            $equations[] = [$clause, '>=', 'start_' . $clause];
            $equations[] = [$clause, '<=', 'end_' . $clause];
            $this->bind['start_' . $clause] = $start;
            $this->bind['end_' . $clause]   = $end;
        }

        if (is_array($clause) && !Arr::isSingleDimension($clause)) {
            foreach (array_shift($clause) as $key => $value) {
                list($start, $end) = explode(',', preg_replace('/ /', '', $value));
                $equations[] = [$key, '>=', 'start_' . $key];
                $equations[] = [$key, '<=', 'end_' . $key];
                $this->bind['start_' . $key] = $start;
                $this->bind['end_' . $key]   = $end;
            }
            $gate = array_shift($clause);
        }

        SQLProcessor::pushOR([$equations, $gate]);
        return $this;
    }

    public function order($field, $orderMode=SQLProcessor::ORDER_ASC)
    {
        SQLProcessor::pushOrder($field, $orderMode);
    }

    public function limit($limit)
    {
        SQLProcessor::pushLimit($limit);
    }

    public function count()
    {
        SQLProcessor::setCommand('COUNT');
        $sql = SQLProcessor::resolve();

        DatabaseProcessor::execute($sql, $this->bind);
               
        $this->bind = [];
        SQLProcessor::clear();
        return DatabaseProcessor::fetchCount();
    }

    public function save()
    {
        SQLProcessor::setCommand('UPDATE');
        $sql = SQLProcessor::resolve();

        DatabaseProcessor::execute($sql, $this->bind);
        
        $this->bind = [];
        SQLProcessor::clear();
    }

    public function insert()
    {
        SQLProcessor::setCommand('INSERT INTO');
        $sql = SQLProcessor::resolve();

        DatabaseProcessor::execute($sql, $this->bind);

        $this->bind = [];
        SQLProcessor::clear();
        return DatabaseProcessor::lastInsertId();
        
    }

    public function delete()
    {
        SQLProcessor::setCommand('DELETE');
        $sql = SQLProcessor::resolve();
        DatabaseProcessor::execute($sql, $this->bind);

        $this->bind = [];
        SQLProcessor::clear();
    }

    public function fetch()
    {
        $sql = SQLProcessor::resolve();
        $result = DatabaseProcessor::execute($sql, $this->bind);

        $this->bind = [];
        SQLProcessor::clear();
        if (!$result) {
            return false;
        }
        $fetchData = DatabaseProcessor::fetch();
        if (is_null($fetchData)) {
            return null;
        }

        foreach ($fetchData as $name => $value) {
            $this->columns[$name] = $value;
        }
        return $this;
    }

    public function fetchAll()
    {
        $sql = SQLProcessor::resolve();
        $result = DatabaseProcessor::execute($sql, $this->bind);
                       
        $this->bind = [];
        SQLProcessor::clear();
        if (!$result) {
            return false;
        }

        $fetchData = DatabaseProcessor::fetchAll();
        if (is_null($fetchData)) {
            return null;
        }

        $data = [];
        $class = get_class($this);
        foreach ($fetchData as $row) {
            $object = new $class;
            foreach ($row as $name => $value) {
                $object->$name = $value;
            }
            $data[] = $object;
        }
        return $data;
    }
}