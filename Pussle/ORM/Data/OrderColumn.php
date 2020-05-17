<?php
namespace Pussle\ORM\Data;

use Pussle\ORM\Interfaces\ColumnInterface;
use Pussle\ORM\Interfaces\SQLInterface;

class OrderColumn implements SQLInterface
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    /** @var ColumnInterface */
    private $column;

    /** @var string */
    private $sort;

    /**
     * @param string $name
     * @param string $sort
     */
    public function __construct(ColumnInterface $column, $sort=self::ASC)
    {
        $this->column = $column;
        $this->sort = $sort;
    }

    public function buildStatement()
    {
        return sprintf(
            '%s %s', 
            !empty($this->column->getAs()) ? '`' . $this->column->getAs() . '`' : $this->column->buildStatement(),
            $this->sort
        );
    }
}