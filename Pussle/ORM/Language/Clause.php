<?php
namespace Pussle\ORM\Language;

use Pussle\ORM\Data\Parameter;
use Pussle\ORM\Interfaces\SQLInterface;

class Clause implements SQLInterface
{
    /** @var array */
    private $parameters = [];

    /** @var array */
    private $clauses = [];

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
     * @param Clause $clause
     */
    public function addClause(Clause $clause)
    {
        $this->clauses[] = $clause;
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
                switch ($operator) {
                    case '=':
                        if (is_null($parameter->getValue())) {
                            $statement = $parameter->buildStatement() . ' IS NULL';
                        } else {
                            $statement = $parameter->buildStatement() . $operator . '?';
                        }

                        break;
                    case '!=':
                    case '<>':
                        if (is_null($parameter->getValue())) {
                            $statement = $parameter->buildStatement() . ' IS NOT NULL';
                        } else {
                            $statement = $parameter->buildStatement() . $operator . '?';
                        }

                        break;
                    case '>=':
                    case '<=':
                        $statement = $parameter->buildStatement() . $operator . '?';
                        break;
                    case 'LIKE':
                        $statement = sprintf('%s LIKE %s', $parameter->buildStatement(), '?');
                        break;
                    case 'BETWEEN':
                        $statement = sprintf('(%s BETWEEN ? AND ?)', $parameter->buildStatement());
                        break;
                    case 'IN':
                    case 'NOT IN':
                        $statement = sprintf('%s %s (%s)', $parameter->buildStatement(), $operator, implode(', ', array_fill(0, count($parameter->getValues()), '?')));
                        break;
                    default:
                        $statement = '';
                }

                return sprintf('%s %s', $relation, $statement);
            }, $this->parameters)),
            implode(' ', array_map(function($clause) {
                return sprintf(
                    '%s (%s)',
                    !empty($clause->getRelation()) ? $clause->getRelation() : '',
                    $clause->buildStatement()
                );
            }, $this->clauses))
        );

    }
}