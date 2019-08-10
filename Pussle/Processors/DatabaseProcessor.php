<?php

namespace Pussle\Processors;

use \PDO;
use Sailor\Core\LoggerFactory as Logger;
use Pussle\DataType\MySQLDataType;

class DatabaseProcessor
{
    const FETCH_ASSOC = PDO::FETCH_ASSOC;

    private $conn;
    private $stmt;
    public function __construct($config)
    {
        try {
            $this->conn = $this->createConnection($config);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            Logger::error('Database connection failed: ' . $e->getMessage());
        }
    }

    public function prepare($sql)
    {
        try {
            if (!empty($this->conn)) {
                $this->stmt = $this->conn->prepare($sql);
            }
        } catch (\PDOException $e) {
            Logger::error('Prepare SQL failed: ' . $e->getMessage());
        }
        return $this;
    }

    public function bind($params, &$value=null)
    {
        try {
            if (is_array($params) && !empty($params)) {
                foreach ($params as $name => &$param) {
                    $this->stmt->bindParam($name, $param);
                }
            }

            if (is_string($params) && !is_null($value)) {
                $this->stmt->bindParam($params, $value);
            }
        } catch (\PDOException $e) {
            Logger::error('Binding params failed: ' . $e->getMessage());
        }
        return $this;
    }

    public function execute($params=null)
    {
        $list = [];
        if (is_array($params) && !empty($params)) {
            foreach ($params as $param) {
                if ($param !== '' && $param !== false && !is_null($param)) {
                    $list[] = $param;
                }
            }
        }

        try {
            $this->stmt->execute(!empty($list) ? $list : null);
        } catch (\PDOException $e) {
            Logger::error($e->getMessage());
            return false;
        }
        return true;
    }

    public function fetch($dataType=self::FETCH_ASSOC)
    {
        try {
            $rs = $this->stmt->fetch($dataType);
        } catch (\PDOException $e) {
            Logger::error('Fetch Failed: ' . $e->getMessage());
            return false;
        }
        return !empty($rs) ? $rs : [];
    }

    public function fetchAll($dataType=self::FETCH_ASSOC)
    {
        try {
            $rs = $this->stmt->fetchAll($dataType);
        } catch (\PDOException $e) {
            Logger::error('Fetch Failed: ' . $e->getMessage());
        }
        return !empty($rs) ? $rs : [];
    }

    public function fetchCount()
    {
        try {
            $rs = $this->stmt->fetch(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            Logger::error('Fetch Failed: ' . $e->getMessage());
        }
        return $rs;
    }

    private function createConnection($config)
    {
        $setting = [];
        $setting['{db}']       = isset($config['db']) ? $config['db'] : 'mysql';
        $setting['{host}']     = isset($config['host']) ? $config['host'] : 'localhost';
        $setting['{dbname}']   = isset($config['dbname']) ? $config['dbname'] : '';
        $setting['{charset}']  = isset($config['charset']) ? $config['charset'] : 'utf8';

        $connection = '{db}:host={host};dbname={dbname};charset={charset}';
        return new PDO(strtr($connection, $setting), $config['username'], $config['password']);
    }
}