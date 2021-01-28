<?php declare(strict_types=1);


namespace core;


use core\database\connection\MySQLConnection;

class Database
{
    // all connection
    protected array $connections = [];

    // 获取默认链接
    protected function getDefaultConnection()
    {
        return \App::getContainer()->get('config')->get('database.default');
    }

    // 设置默认链接
    public function setDefaultConnection($name)
    {
        \App::getContainer()->get('config')->set('database.default', $name);
    }

    // 根据配置信息的name来创建链接
    public function connection($name = null)
    {
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }
        if ($name == null) {
            $name = $this->getDefaultConnection();
        }
        // 获取链接配置
        $config = \App::getContainer()->get('config')->get('database.connections.'. $name);

        $connectionClass = '';
        switch ($config['driver']) {
            case 'mysql':
                $connectionClass = MySQLConnection::class;
                break;
            // 其他类型的数据库
        }

        $dns = sprintf("%s:host=%s;dbname=%s", $config['driver'], $config['host'], $config['dbname']);
        try {
            $pdo = new \PDO($dns, $config['username'], $config['password'], $config['options']);
        }catch (\PDOException $PDOException) {
            exit($PDOException->getMessage());
        }

        return $this->connections[$name] = new $connectionClass($pdo, $config);
    }

    // 代理模式
    public function __call($name, $arguments)
    {
        return $this->connection()->$name(...$arguments);
    }

}