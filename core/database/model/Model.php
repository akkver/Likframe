<?php declare(strict_types=1);


namespace core\database\model;


use Cassandra;
use Cassandra\Cluster\Builder;

class Model
{
    protected ?object $connection = null;
    protected string $table;
    protected string $primaryKey;
    protected bool $timestamps = true;

    /**
     *  为什么要分开两个属性?
     * $original 原的数据
     * $attribute 原数据的复制版 用户只能修改这个 !
     * 然后跟$original相比较 得出用户修改的数据字段
     */
    protected object $original;
    protected object $attribute;

    public function __construct()
    {
        // 当前模型绑定数据库链接
        $this->connection = \App::getContainer()->get('db')->connection($this->connection);
    }

    /**
     * 获取表名 没有表名就返回 模型（小写）+s
     * @return string
     */
    public function getTable()
    {
        if ($this->table) {
            return $this->table;
        }

        $className = get_class($this);
        $classArr  = explode('\\', $className);

        $table = lcfirst(end($classArr));

        return $table.'s';
    }

    public function setOriginal($key, $val)
    {
        if (! $this->original) {
            $this->original = new \stdClass();
        }
        $this->original->$key = $val;
    }

    public function setAttribute($key, $val)
    {
        if (! $this->attribute) {
            $this->attribute  = new \stdClass();
        }
        $this->attribute->$key = $val;
    }

    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    // 属性同步original
    public function syncOriginal()
    {
        $this->attribute = $this->original;
    }

    /**
     * 返回用户修改过的数据
     * @return array
     * @example ['id' => 3,'user_id' => '3']
     */
    public function diff()
    {
        $diff = [];
        if ($this->attribute == $this->original) {
            return $diff;
        }

        foreach ($this->original as $originKey => $originVal) {
            if ($this->attribute->$originKey != $originVal) {
                $diff[$originKey] = $this->attribute->$originKey;
            }
        }

        return $diff;
    }

    public function __get($name)
    {
        return $this->attribute->$name;
    }

    public function __call($name, $arguments)
    {
        return (new Builder($this->connection->newBuilder()))
            ->setModel($this)
            ->$name(...$arguments);
    }

    /**
     * 托管到 __call
     * so, User::where() 与 (new User)->where() 相同
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        //  new self() 和 new static 的区别
        //  new static()是在php5.3版本引入的新特性
        //  他们的区别只有在继承中才能体现出来、如果没有任何继承、那么二者没有任何区别
        //  然后 new self() 返回的实列是不会变的，无论谁去调用，都返回的一个类的实列，
        //  而 new static则是由调用者决定的，返回的是调用者的实列
        return (new static())->$name(...$arguments);
    }

}