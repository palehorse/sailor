<?php
namespace Pussle\ORM\Models;

use Pussle\ORM\Traits\DataSourceTrait;

class Table
{
    use DataSourceTrait;

    /** @var string */
    private $name;

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
     * @param string $name
     */
    public function addColumn($name)
    {
        if (!isset($this->columns[$name])) {
            $this->columns[$name] = null;
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setColumn($name, $value)
    {
        if ($this->checkColumnExists($name) && (is_string($value) || is_int($value))) {
            $this->columns[$name] = $value;
        }
    }
    
    public function removeColumn($name)
    {
        if ($this->checkColumnExists($name)) {
            unset($this->columns[$name]);
        }
    }

    protected function checkColumnExists($name)
    {
        return in_array($name, array_keys($this->columns));
    }
}