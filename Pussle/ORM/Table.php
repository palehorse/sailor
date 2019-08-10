<?php

namespace Pussle\ORM;

use \PDO;
use Sailor\Core\LoggerFactory as Logger;
use Sailor\Utility\Arr;
use Sailor\Processors\DatabaseProcessor;
use Sailor\Processors\SQLProcessor;

class Table
{
    protected $table;
    protected $conn;
    protected $countField;
    protected $columns = [];
    protected $bind = [];
    protected $DatabaseProcessor;
    protected $SQLProcessor;
    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->DatabaseProcessor = new DatabaseProcessor($config);
        $this->SQLProcessor = new SQLProcessor($this->table);
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
            $this->SQLProcessor->addFields($name);
            $this->bind[$name] = $value;
        }
    }

    protected function fetchColumns()
    {
        $sql = "SELECT COLUMN_NAME FROM Information_Schema.COLUMNS WHERE TABLE_NAME='$this->table'";
        $this->DatabaseProcessor
             ->prepare($sql)
             ->execute();
        $columns = $this->DatabaseProcessor->fetchAll(DatabaseProcessor::FETCH_ASSOC);
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
            $this->SQLProcessor->addFields($fields);
        }

        if (is_string($fields)) {
            if ($fields == '*') {
                $this->SQLProcessor->addFields(array_keys($this->columns));
            } else {
                $this->SQLProcessor->addFields($fields);
            }
        }

        $this->SQLProcessor
             ->setCommand('SELECT');
        
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

        $this->SQLProcessor->pushAND([$equations, $gate]);
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

        $this->SQLProcessor->pushOR([$equations, $gate]);
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

        $this->SQLProcessor->pushAND([$equations, $gate]);
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

        $this->SQLProcessor->pushOR([$equations, $gate]);
        return $this;
    }

    public function order($field, $orderMode=SQLProcessor::ORDER_ASC)
    {
        $this->SQLProcessor->pushOrder($field, $orderMode);
    }

    public function limit($limit)
    {
        $this->SQLProcessor->pushLimit($limit);
    }

    public function count()
    {
        $sql = $this->SQLProcessor
                    ->setCommand('COUNT')
                    ->resolve();

        $this->DatabaseProcessor
             ->prepare($sql)
             ->bind($this->bind)
             ->execute();
               
        $this->bind = [];
        $this->SQLProcessor->clear();
        return $this->DatabaseProcessor->fetchCount();
    }

    public function save()
    {
        $sql = $this->SQLProcessor
                    ->setCommand('UPDATE')
                    ->resolve();

        $this->DatabaseProcessor
             ->prepare($sql)
             ->bind($this->bind)
             ->execute();
        
        $this->bind = [];
        $this->SQLProcessor->clear();
    }

    public function insert()
    {
        $sql = $this->SQLProcessor
                    ->setCommand('INSERT INTO')
                    ->resolve();

        $this->DatabaseProcessor
             ->prepare($sql)
             ->bind($this->bind)
             ->execute();

        $this->bind = [];
        $this->SQLProcessor->clear();
        return $this->DatabaseProcessor->fetch();
    }

    public function delete()
    {
        $sql = $this->SQLProcessor
                    ->setCommand('DELETE')
                    ->resolve();
                    
        $this->DatabaseProcessor
             ->prepare($sql)
             ->bind($this->bind)
             ->execute();

        $this->bind = [];
        $this->SQLProcessor->clear();
    }

    public function fetch()
    {
        $sql = $this->SQLProcessor->resolve();
        $result = $this->DatabaseProcessor
                       ->prepare($sql)
                       ->bind($this->bind)
                       ->execute();

        $this->bind = [];
        $this->SQLProcessor->clear();
        if (!$result) {
            return false;
        }
        return $this->DatabaseProcessor->fetch();
    }

    public function fetchAll()
    {
        $sql = $this->SQLProcessor->resolve();

        $result = $this->DatabaseProcessor
                       ->prepare($sql)
                       ->bind($this->bind)
                       ->execute();
                       
        $this->bind = [];
        $this->SQLProcessor->clear();
        if (!$result) {
            return false;
        }
        return $this->DatabaseProcessor->fetchAll();
    }
}