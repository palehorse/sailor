<?php
namespace Pussle\ORM\SQL;

use Pussle\ORM\Models\Table;

class Insert implements Command
{
    /** @var Table */
    private $table;

    /** @var $isIgnore */
    private $isIgnore = false;

    /** @var array */
    private $params = [];

    public function __construct(Table $table, $isIgnore = false)
    {
        $this->table = $table;
        $this->isIgnore = $isIgnore;
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
            $columns[] = $name;
            $this->params[] = $value;
        }

        $columns = array_map(function($column) {
            return sprintf('`%s`', $column);
        }, $columns);

        $sqlComponents = [
            'INSERT INTO',
            '`' . $table->getName() . '`',
            sprintf('(%s)', implode(',', $columns)),
            'VALUES',
            sprintf('(%s)', implode(',', array_fill(0, count($columns), '?'))),
        ];

        return implode(' ', $sqlComponents);
    }

    public function setWhere(Where $where) {}
    public function getWhere() {}
}