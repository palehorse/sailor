<?php

namespace Pussle\Processors;

use Sailor\Utility\Arr;

class SQLProcessor
{
    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';

    private $table;
    private $fields = [];
    private $command;
    private $where = [];
    private $order = [];
    private $limit;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function setCommand($command)
    {
        if (in_array(strtoupper($command), ['SELECT', 'COUNT', 'INSERT INTO', 'UPDATE', 'DELETE'])) {
            $this->command = strtoupper($command);
        }
        return $this;
    }

    public function addFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', preg_replace('/ /', '', $fields));
        }

        if (!empty($fields) && Arr::isSingleDimension($fields)) {
            foreach ($fields as $name) {
                if (!in_array($name, $this->fields)) {
                    $this->fields[] = $name;
                }
            }
        }
        
        return $this;
    }

    public function pushAND($clause)
    {
        if (Arr::isSingleDimension($clause)) {
            return $this;
        }

        if (!empty($this->where)) {
            $this->where[] = "AND";
        }
        $this->where[] = $this->translate($clause);
        return $this;
    }

    public function pushOR($clause)
    {
        if (Arr::isSingleDimension($clause)) {
            return $this;
        }
        if (!empty($this->where)) {
            $this->where[] = "OR";
        }
        $this->where[] = $this->translate($clause);
        return $this;
    }

    public function pushOrder($field, $orderMode=self::ORDER_ASC)
    {
        $orderMode = ($orderMode === self::ORDER_DESC) ? $orderMode : self::ORDER_ASC;
        $this->order[$field] = $orderMode;
        return $this;
    }

    public function pushLimit($limit)
    {
        if (is_int($limit)) {
            $this->limit = $limit;
        }
        return $this;
    }

    public function resolve()
    {
        return $this->composite();
    }

    private function composite()
    {
        switch ($this->command) {
            case "SELECT":
                $sqlSlots = [$this->command];
                $sqlSlots[] = implode(", ", array_map(function($value) {
                    if ($value !== '' && $value !== false) {
                        return "`" . $value . "`";
                    }    
                }, $this->fields));
                
                $sqlSlots[] = "FROM";
                $sqlSlots[] = "`" . $this->table . "`";
                if (!empty($this->order)) {
                    $sqlSlots[] = "ORDER BY";
                    $order = [];
                    foreach ($this->order as $field => $mode) {
                        $order[] = "`" . $field . "` " . $mode;
                    }
                    $sqlSlots[] = implode(', ', $order);
                }

                if (!empty($this->limit)) {
                    $sqlSlots[] = "LIMIT " . $this->limit;
                }
                
                break;
            case 'COUNT':
                $sqlSlots[] = "SELECT";
                $sqlSlots[] = "COUNT(`" . $this->fields[0] . "`) AS count_" . $this->fields[0];
                $sqlSlots[] = "FROM";
                $sqlSlots[] = "`" . $this->table . "`";
                if (!empty($this->limit)) {
                    $sqlSlots[] = "LIMIT " . $this->limit;
                }
                break;
            case 'INSERT INTO':
                $sqlSlots = [$this->command];
                $sqlSlots[] = "`" . $this->table . "`";
                $sqlSlots[] = "(" . implode(", ", array_map(function($value) {
                    if ($value !== '' && $value !== false && !is_null($value)) {    
                        return "`" . $value . "`";
                    }
                }, $this->fields)) . ")";
                $sqlSlots[] = "VALUES";
                $sqlSlots[] = "(" . implode(", ", array_map(function($value) {
                    if ($value !== '' && $value !== false && !is_null($value)) {
                        return ":" . $value;
                    }
                    return null;
                }, $this->fields)) . ")";
                break;
            case "UPDATE":
                $sqlSlots = [$this->command];
                $sqlSlots[] = "`" . $this->table . "`";
                $sqlSlots[] = "SET";
                $sqlSlots[] = implode(', ', array_map(function($name) {
                    return "`" . $name . "`=:" . $name;
                }, $this->fields));
                break;
            case "DELETE":
                $sqlSlots = [$this->command];
                $sqlSlots[] = "FROM";
                $sqlSlots[] = "`" . $this->table . "`";
                break;
            default:
                return null;
        }

        if (!empty($this->where)) {
            $sqlSlots[] = "WHERE";
            $sqlSlots[] = implode(" ", $this->where);
        }
        return implode(" ", $sqlSlots);
    }

    private function translate($clause)
    {
        $fields = array_shift($clause);
        $gate   = array_shift($clause);
        switch ($gate) {
            default:
            case '&':
                $gate = " AND ";
                break;
            case '|':
                $gate = " OR ";
                break;
        }

        $snippet = [];
        foreach ($fields as $row) {
            list($name, $operator, $variable) = $row;
            $snippet[] = "`$name` $operator :$variable";
        }

        if (count($snippet) > 1) {
            return "(" . implode($gate, $snippet) . ")";
        }
        return implode($gate, $snippet);
    }

    public function clear()
    {
        $this->command = null;
        $this->fields = [];
        $this->where = [];
        $this->order = [];
    }
}