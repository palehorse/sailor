<?php
namespace Pussle\ORM\SQL;

use Pussle\ORM\Models\Table;

class Delete implements Command
{
    /** @var Table */
    private $table;

    /** @var Where */
    private $where;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @param Where $where
     */
    public function setWhere(Where $where)
    {
        $this->where = $where;
    }

    /**
     * @return Where
     */
    public function getWhere()
    {
        return $this->where;
    }

    public function build()
    {
        $table = $this->table;
        $sqlComponents = [
            'DELETE FROM',
            '`' . $table->getName() . '`',
        ];

        if (!is_null($this->where)) {
            $sqlComponents[] = 'WHERE';
            $sqlComponents[] = $this->where->build();
        }

        return implode(' ', $sqlComponents);
    }
}