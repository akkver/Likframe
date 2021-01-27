<?php declare(strict_types=1);


namespace core\database\connection;


class Connection
{
    protected object $pdo;
    protected string $tablePrefix = '';
    protected array $config = [];

    public function __construct($pdo, $config)
    {
        $this->pdo = $pdo;
        $this->tablePrefix = $config['prefix'];
        $this->config = $config;
    }

}