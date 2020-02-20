<?php
namespace Pussle\ORM\DB;

use PDO;
use PDOStatement;

class AccessResult
{
    /** @var PDOStatement */
    private $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function fetch()
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}