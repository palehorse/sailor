<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Column;
use Pussle\ORM\Interfaces\SQLInterface;

class On implements SQLInterface
{
    /** @var array */
    private $statements = [];

    /**
     * @param Column $leftColumn
     * @param Column $rightColumn
     */
    public function addStatement(Column $leftColumn, Column $rightColumn)
    {
        $this->statements[] = [$leftColumn, $rightColumn];
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        return implode(' AND ', array_map(function($statement) {
            list($leftColumn, $rightColumn) = $statement;
            return sprintf(
                '%s=%s', 
                $leftColumn->buildStatement(),
                $rightColumn->buildStatement()
            );
        }, $this->statements));
    }
}