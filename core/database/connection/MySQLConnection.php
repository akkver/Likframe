<?php declare(strict_types=1);


namespace core\database\connection;


use core\query\MysqlGrammar;
use core\query\QueryBuilder;

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

    // 调用一个不存在的方法  调用一个新的查询构造器
    public function __call($name, $arguments)
    {
        // 返回QueryBuilder类
        return $this->newBuilder()->$name(...$arguments);
    }

    // 构造新的查询器
    public function newBuilder()
    {
        return new QueryBuilder($this, new MysqlGrammar());
    }

}
