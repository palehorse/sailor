<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Interfaces\SQLInterface;

class Limit implements SQLInterface
{
    /** @var integer */
    private $number;

    /** @var integer */
    private $offset;

    public function __construct($number, $offset=null)
    {   
        $this->number = $number;
        if (!is_null($offset)) {
            $this->offset = $offset;
        }
    }

    /**
     * @return string | the SQL statement
     */
    public function buildStatement()
    {
        return sprintf('%s %s%s', 'LIMIT', !is_null($this->offset) ? ' ' . $this->offset . ', ' : '', $this->number);
    }
}