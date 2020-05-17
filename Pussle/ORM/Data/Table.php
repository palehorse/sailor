<?php
namespace Pussle\ORM\Data;

use Pussle\ORM\Interfaces\SQLInterface;
use Pussle\ORM\Traits\AliasTrait;

class Table implements SQLInterface
{
    use AliasTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $alias;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        return !empty($this->alias) ? '`' . $this->alias . '`' : '`' . $this->name . '`';
    }
}