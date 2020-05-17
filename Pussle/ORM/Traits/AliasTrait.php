<?php
namespace Pussle\ORM\Traits;

trait AliasTrait
{
    public function setAs($alias)
    {
        $this->alias = $alias;
    }

    public function getAs()
    {
        return $this->alias;
    }
}