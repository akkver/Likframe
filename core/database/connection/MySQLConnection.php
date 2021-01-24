<?php declare(strict_types=1);


namespace core\database\connection;


use core\database\Connection;

class MySQLConnection extends Connection
{
    protected static \stdClass $connection;

    public function getConnection()
    {
        return self::$connection;
    }

    // 执行SQL
    public function select($sql, $bindings = [], $useReadPdo = true)
    {
        $statement = $this->pdo;
        $sth = $statement->prepare($sql);
        try {
            $sth->execute($bindings);
            return $sth->fetchAll();
        } catch (\PDOException $PDOException) {
            echo $PDOException->getMessage();
        }
    }
}