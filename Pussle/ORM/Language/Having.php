<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Parameter;
use Pussle\ORM\Interfaces\SQLInterface;

class Having implements SQLInterface
{
    /** @var array */
    private $parameters = [];

    /** @var array */
    private $havings = [];

    /** @var string */
    private $relation;

    /**
     * @param string $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param Parameter $parameter
     */
    public function addParameter(Parameter $parameter)
    {
        $this->parameters[] = $parameter;
    }

    /**
     * @param Having $having
     */
    public function addHaving(Having $having)
    {
        $this->havings[] = $having;
    }

    /** 
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @return string 
     */
    public function buildStatement()
    {
        return sprintf(
            '%s %s',
            implode(' ', array_map(function($parameter) {
                $relation = !empty($parameter->getRelation()) ? $parameter->getRelation() : '';
                $operator = $parameter->getOperator();
                $name = !empty($parameter->getColumn()->getAs()) ? '`' . $parameter->getColumn()->getAs() . '`' : $parameter->getColumn()->buildStatement();
                switch ($operator) {
                    case '=':
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                    case '!=':
                    case '<>':
                        $statement = $name . $operator . '?';
                        break;
                    case 'LIKE':
                        $statement = sprintf('%s LIKE %s', $name, '?');
                        break;
                    case 'BETWEEN':
                        $statement = sprintf('(%s BETWEEN ? AND ?)', $name);
                        break;
                    case 'IN':
                        $statement = sprintf('%s %s (%s)', $name, implode(', ', $operator, array_fill(0, count($parameter->getValues()), '?')));
                        break;
                    default:
                        $statement = '';
                }

                return sprintf('%s %s', $relation, $statement);
            }, $this->parameters)),
            implode(' ', array_map(function($having) {
                return sprintf(
                    '%s (%s)',
                    !empty($having->getRelation()) ? $having->getRelation() : '',
                    $having->buildStatement()
                );
            }, $this->havings))
        );

    }
}