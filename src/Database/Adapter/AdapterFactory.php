<?php

namespace Phoenix\Database\Adapter;

use PDO;
use Phoenix\Config\EnvironmentConfig;
use Phoenix\Exception\InvalidArgumentValueException;

class AdapterFactory
{
    private static $instances = [];

    public static function instance(EnvironmentConfig $config)
    {
        $configHash = md5(json_encode($config->getConfiguration()));
        if (isset(self::$instances[$configHash])) {
            return self::$instances[$configHash];
        }
        $pdo = new PDO($config->getDsn(), $config->getUsername(), $config->getPassword());
        if ($config->getAdapter() == 'mysql') {
            $adapter = new MysqlAdapter($pdo);
        } elseif ($config->getAdapter() == 'pgsql') {
            $adapter = new PgsqlAdapter($pdo);
        } elseif ($config->getAdapter() == 'sqlite') {
            $adapter = new SqliteAdapter($pdo);
        } else {
            throw new InvalidArgumentValueException('Unknown adapter "' . $config->getAdapter() . '". Use one of value: "mysql", "pgsql", "sqlite".');
        }
        $adapter->setCharset($config->getCharset());
        self::$instances[$configHash] = $adapter;
        return $adapter;
    }
}
