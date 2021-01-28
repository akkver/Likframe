<?php


namespace core\database\model;


class Builder
{
    protected object $query;
    protected object $model;

    public function __construct($query)
    {
        $this->query =  $query;
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    public function __call($name, $arguments)
    {
        $this->query->$name(...$arguments);
        return $this;
    }

    public function get($columns = ['*'])
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }
        $this->query->columns = $columns;
        $this->query->table($this->model->getTable());
        $sql = $this->query->toSQL();
        return $this->bindModel($this->query->runSQL($sql));
    }

    /**
     * 数据映射模式   把数据映射到模型
     * 模型的本质： 即每一条数据都是一个模型（对象）
     * @param $datas
     * @return array
     */
    public function bindModel($datas)
    {
        if (!is_array($datas)) {
            $datas[] = $datas;
        }
        $models = [];
        foreach ($datas as $data) {
            $model = clone $this->model;
            foreach ($data as $key => $value) {
                $this->setOriginal($key, $value);
            }
            $this->syncOriginal();
            $models[] = $model;
        }

        return $models;
    }

}