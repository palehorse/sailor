<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Table;
use Pussle\ORM\Interfaces\DMLInterface;
use Pussle\ORM\Traits\DMLTrait;

class Delete implements DMLInterface
{
    use DMLTrait;

    /** @var Table */
    private $table;

    /**
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        return 'DELETE FROM ' . $this->table->buildStatement();
    }
}