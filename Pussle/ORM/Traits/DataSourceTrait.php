<?php
namespace Pussle\ORM\Traits;

trait DataSourceTrait
{
    /** @var string */
    protected $alias;

    /** @var array */
    protected $columns = [];

    /**
     * @param string $alias
     */
    public function setAlias($alias) 
    {    
        $this->alias = $alias;
    }

    /** 
     * @return string
     */
    public function getAlias() 
    {
        return $this->alias;
    }
    
    /**
     * @return array
     */
    public function getColumns() 
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getColumnNames() {
        return array_keys($this->columns);
    }
}