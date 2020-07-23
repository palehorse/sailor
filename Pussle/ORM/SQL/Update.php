<?php
namespace Pussle\ORM\SQL;

use Pussle\ORM\Models\Table;

class Update implements Command
{
    /** @var Table */
    private $table;

    /** @var Where */
    private $where;
    
    /** @var array */
    private $params = [];

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

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    public function build()
    {
        $table = $this->table;
        $columns = [];
        foreach ($table->getColumns() as $name => $value) {
            if (!is_null($value)) {
                $columns[] = $name . '=?';
                $this->params[] = $value;
            }
        }

        $sqlComponents = [
            'UPDATE',
            '`' . $table->getName() . '`',
            'SET',
            implode(',', $columns),
        ];

        if (!is_null($this->where)) {
            $sqlComponents[] = 'WHERE';
            $sqlComponents[] = $this->where->build();
        }

        return implode(' ', $sqlComponents);
    }
}