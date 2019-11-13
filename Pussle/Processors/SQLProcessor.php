<?php

namespace Pussle\Processors;

use Sailor\Utility\Arr;

class SQLProcessor
{
    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';

    private static $table;
    private static $fields = [];
    private static $command;
    private static $where = [];
    private static $order = [];
    private static $limit;

    public static function setTable($table)
    {
        self::$table = $table;
    }

    public static function setCommand($command)
    {
        if (in_array(strtoupper($command), ['SELECT', 'COUNT', 'INSERT INTO', 'UPDATE', 'DELETE'])) {
            self::$command = strtoupper($command);
        }
    }

    public static function addFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', preg_replace('/ /', '', $fields));
        }

        if (!empty($fields) && Arr::isSingleDimension($fields)) {
            foreach ($fields as $name) {
                if (!in_array($name, self::$fields)) {
                    self::$fields[] = $name;
                }
            }
        }
    }

    public static function pushAND($clause)
    {
        if (Arr::isSingleDimension($clause)) {
            return;
        }

        if (!empty(self::$where)) {
            self::$where[] = "AND";
        }
        self::$where[] = self::translate($clause);
    }

    public static function pushOR($clause)
    {
        if (Arr::isSingleDimension($clause)) {
            return;
        }
        if (!empty(self::$where)) {
            self::$where[] = "OR";
        }
        self::$where[] = self::translate($clause);
    }

    public static function pushOrder($field, $orderMode=self::ORDER_ASC)
    {
        $orderMode = ($orderMode === self::ORDER_DESC) ? $orderMode : self::ORDER_ASC;
        self::$order[$field] = $orderMode;
    }

    public static function pushLimit($limit)
    {
        if (is_int($limit)) {
            self::$limit = $limit;
        }
    }

    public static function resolve()
    {
        return self::composite();
    }

    public static function clear()
    {
        self::$command = null;
        self::$fields = [];
        self::$where = [];
        self::$order = [];
    }

    private static function composite()
    {
        switch (self::$command) {
            case "SELECT":
                $sqlSlots = [self::$command];
                $sqlSlots[] = implode(", ", array_map(function($value) {
                    if ($value !== '' && $value !== false) {
                        return "`" . $value . "`";
                    }    
                }, self::$fields));
                
                $sqlSlots[] = "FROM";
                $sqlSlots[] = "`" . self::$table . "`";
                if (!empty(self::$order)) {
                    $sqlSlots[] = "ORDER BY";
                    $order = [];
                    foreach (self::$order as $field => $mode) {
                        $order[] = "`" . $field . "` " . $mode;
                    }
                    $sqlSlots[] = implode(', ', $order);
                }

                if (!empty(self::$limit)) {
                    $sqlSlots[] = "LIMIT " . self::$limit;
                }
                
                break;
            case 'COUNT':
                $sqlSlots[] = "SELECT";
                $sqlSlots[] = "COUNT(`" . self::$fields[0] . "`) AS count_" . self::$fields[0];
                $sqlSlots[] = "FROM";
                $sqlSlots[] = "`" . self::$table . "`";
                if (!empty(self::$limit)) {
                    $sqlSlots[] = "LIMIT " . self::$limit;
                }
                break;
            case 'INSERT INTO':
                $sqlSlots = [self::$command];
                $sqlSlots[] = "`" . self::$table . "`";
                $sqlSlots[] = "(" . implode(", ", array_map(function($value) {
                    if ($value !== '' && $value !== false && !is_null($value)) {    
                        return "`" . $value . "`";
                    }
                }, self::$fields)) . ")";
                $sqlSlots[] = "VALUES";
                $sqlSlots[] = "(" . implode(", ", array_map(function($value) {
                    if ($value !== '' && $value !== false && !is_null($value)) {
                        return ":" . $value;
                    }
                    return null;
                }, self::$fields)) . ")";
                break;
            case "UPDATE":
                $sqlSlots = [self::$command];
                $sqlSlots[] = "`" . self::$table . "`";
                $sqlSlots[] = "SET";
                $sqlSlots[] = implode(', ', array_map(function($name) {
                    return "`" . $name . "`=:" . $name;
                }, self::$fields));
                break;
            case "DELETE":
                $sqlSlots = [self::$command];
                $sqlSlots[] = "FROM";
                $sqlSlots[] = "`" . self::$table . "`";
                break;
            default:
                return null;
        }

        if (!empty(self::$where)) {
            $sqlSlots[] = "WHERE";
            $sqlSlots[] = implode(" ", self::$where);
        }
        return implode(" ", $sqlSlots);
    }

    private static function translate($clause)
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
}