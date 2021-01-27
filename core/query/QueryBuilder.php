<?php


namespace core\query;



use core\database\connection\Connection;

class QueryBuilder
{
    protected Connection $connection;
    protected object $grammar;
    public array $binds;
    public array $columns;
    public int $distinct = 0;
    public string $from;
    public string $union;
    // private array $wheres;

    public array $bindings = [
        'select' => [],
        'join' => [],
        'from' => [],
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'order' => [],
        'limit' => [],
        'union' => [],
        'unionOrder' => [],
    ];

    protected array $operators = [
        '=','<','>','<=','>=','<>','!=','<=>','like','like binary','not like','ilike','&','|','^',
        '<<','>>','rlike','not rlike','regexp','not regexp','~','~*','!~','!~*','similar to','not similar to',
        'not ilike','~~*','!~~*'
    ];

    public function __construct(Connection $connection, $grammar)
    {
        // 数据库链接
        $this->connection = $connection;
        // 编译成SQL的类
        $this->grammar = $grammar;
    }

    public function table(string $table, $as = null)
    {
        return $this->from($table, $as);
    }

    public function from($table, $as)
    {
        $this->from = $as ? "{$table} as {$as}" : $table;
        return $this;
    }

    public function get($columns = ['*'])
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }

        $this->columns = $columns;
        $sql = $this->toSQL();
        return $this->runSQL($sql);
    }

    public function toSQL()
    {
        return $this->grammar->compileSQL($this);
    }

    public function runSQL(string $sql)
    {
        return $this->connection->select($sql, $this->getBinds());
    }

    public function getBinds()
    {
        return $this->binds;
    }

    public function where($column, $operator = null, $value = null, $joiner = 'and')
    {
        if (is_array($column)) {
            foreach ($column as $col => $value) {
                $this->where($col, '=', $value);
            }
        }

        // 如果操作符不存在
        if (! in_array($operator, $this->operators)) {
            $value = $operator;
            $operator = '=';
        }

        $type = "Basic";
        $this->wheres[] = compact(
            'type', 'column', 'operator', 'value', 'joiner'
        );
        $this->binds[] = $value;
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    public function find($id, $columns = ['*'], $key = 'id')
    {
        return $this->where($key, $id)->get($columns);
    }

    public function whereLike($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'like');
    }

}