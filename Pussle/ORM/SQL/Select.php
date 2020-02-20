<?php
namespace Pussle\ORM\SQL;

use Pussle\ORM\Models\Table;
use Pussle\ORM\Traits\DataSourceTrait;

class Select implements Command
{
    use DataSourceTrait;

    /** @var mixed */
    private $source;

    /** @var array */
    private $count = [];

    /** @var array */
    private $group = [];

    /** @var array */
    private $order = [];

    /** @var Where */
    private $where;

    public function __construct($source, array $count = [], array $group = [], array $order = [])
    {
        $this->source = $source;
        $this->columns = $this->source->getColumns();
        $this->count = $count;
        $this->group = $group;
        $this->order = $order;
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
     * @return string
     */
    public function build()
    {
        $source = $this->source;

        if ($source instanceof Select) {
            $name = $source->build();
        }

        if ($source instanceof Table) {
            $name = $source->getName();
        }

        $columns = array_map(function($name) {
            return $name;
        }, array_keys($this->columns));

        $count = array_map(function($field) {
            if (is_array($field) && isset($field['as'])) {
                return sprintf('COUNT(%s) AS %s', $field[0], $field['as']);
            }
            return 'COUNT(' . $field . ')';
        }, $this->count);

        $sqlComponents = [
            'SELECT',
            implode(',', $columns) . (!empty($count) ? ',' . implode(',', $count) : ''),
            'FROM',
            '`' . $name . '`',
        ];
        
        if (!is_null($this->where)) {
            $sqlComponents[] = 'WHERE';
            $sqlComponents[] = $this->where->build();
        }

        if (!empty($this->group)) {
            $sqlComponents[] = 'GROUP BY';
            $sqlComponents[] = implode(',', $this->group);
        }

        if (!empty($this->order)) {
            $sqlComponents[] = 'ORDER BY';
            $order = $this->order;
            $sequence = array_pop($order);
            if (!in_array($sequence, ['ASC', 'DESC'])) {
                $sqlComponents[] = implode(',', $this->order);
                $sqlComponents[] = 'ASC';
            } else {
                $sqlComponents[] = implode(',', $order);
                $sqlComponents[] = $sequence;
            }
        }

        return implode(' ', $sqlComponents);
    }
}