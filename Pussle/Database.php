<?php
namespace Pussle;

use PDO;
use Phinx\Console\Command\Status;
use Pussle\ORM\Language\Insert;
use Pussle\ORM\Language\SQLStatement;
use Sailor\Core\Config;
use Pussle\Statement;

class Database
{
    /** @var PDO */
    private static $connection;

    public static function execute(SQLStatement $sqlStatement)
    {
        $connection = self::getInstance();
        $sql = $sqlStatement->buildStatement();
        $stmt = $connection->prepare($sql);

        $parameterValues = [];   
        $dml = $sqlStatement->getDML();

        if (method_exists($dml, 'getParameters')) {
            if ($dml instanceof Insert) {
                $params = $dml->getParameters();
                for ($i=0; $i<count($params[0]->getValues()); $i++) {
                    $parameterValues = array_merge($parameterValues, array_map(function($parameter) use ($i) {
                        return $parameter->getValue($i);
                    }, $dml->getParameters()));
                }
            } else {
                foreach ($dml->getParameters() as $parameter) {
                    $parameterValues[] = $parameter->getValue();
                }
            }
        }

        foreach ($sqlStatement->getClauses() as $clause) {
            $parameterValues = array_merge($parameterValues, array_reduce($clause->getParameters(), function($values, $parameter) {
                $parameters = array_reduce($parameter->getValues(), function($parameters, $value) {
                    if (!is_null($value)) {
                        $parameters[] = $value;
                    }

                    return $parameters;
                }, []);
                return array_merge($values, $parameters);
            }, []));
        }
        
        $group = $sqlStatement->getGroup();
        if (!empty($group) && !empty($group->getHavings())) {
            foreach ($group->getHavings() as $having) {
                $parameterValues = array_merge($parameterValues, array_reduce($having->getParameters(), function($values, $parameter) {
                    return array_merge(!empty($values) ? $values : [], $parameter->getValues());
                }));
            }
        }

        $stmt->execute($parameterValues);
        return new Statement($stmt);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return Statement
     */
    public static function run($sql, array $params)
    {
        $connection = self::getInstance();
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        return new Statement($stmt);
    }

    public static function beginTransaction()
    {
        $connection = self::getInstance();
        return $connection->beginTransaction();
    }

    public static function commit()
    {
        $connection = self::getInstance();
        return $connection->commit();
    }

    public static function rollback()
    {
        $connection = self::getInstance();
        return $connection->rollBack();
    }

    public static function lastInsertId()
    {
        $connection = self::getInstance();
        return $connection->lastInsertId();
    }

    private static function getInstance()
    {
        return !empty(self::$connection) ? self::$connection : self::$connection = self::createConnection();
    }

    private static function createConnection()
    {
        $setting = [];
        $setting['{db}']       = 'mysql';
        $setting['{host}']     = Config::get('database.DB_HOST');
        $setting['{dbname}']   = Config::get('database.DB_NAME');
        $setting['{charset}']  = Config::get('database.DB_CHARSET');

        $connection = '{db}:host={host};dbname={dbname};charset={charset}';
        return new PDO(strtr($connection, $setting), Config::get('database.DB_USER'), Config::get('database.DB_PWD'));
    }
}