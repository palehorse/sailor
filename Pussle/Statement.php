<?php
namespace Pussle\ORM;

use PDO;
use stdClass;

class Statement
{
    private $stmt;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    /**
     * @return Object
     */
    public function fetch()
    {
        if (empty($this->stmt)) {
            return null;
        }

        $data = $this->stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($data)) {
            return null;
        }

        return $this->toStdClass($data);
    }

    /**
     * @return array   
     */
    public function fetchAll()
    {
        if (empty($this->stmt)) {
            return [];
        }
        
        $data = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($data)) {
            return [];
        }

        return $this->toStdClass($data);
    }

    private function toStdClass(array $data)
    {
        $isSingleDimensionArray = empty(array_filter($data, function($item) {
            return is_array($item);
        }));

        if ($isSingleDimensionArray) {
            $stdClass = new stdClass;
            foreach ($data as $name => $value) {
                $stdClass->$name = $value;
            }

            return $stdClass;
        }

        $list = [];
        foreach ($data as $row) {
            $stdClass = new stdClass;
            foreach ($row as $name => $value) {
                $stdClass->$name = $value;
            }

            $list[] = $stdClass;
        }

        return $list;
    }
}