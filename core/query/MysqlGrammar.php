<?php


namespace core\query;


class MysqlGrammar
{
    protected array $selectComponents = [
        'columns',
        'joins',
        'from',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'lock',
    ];

    public function compileSQL(QueryBuilder $query)
    {
        $sql = [];
        foreach ($this->selectComponents as $component) {
            if (isset($query->{$component})) {
                $sql[$component] = $this->$component($query, $query->$component);
            }
        }
        return implode($sql);
    }

    protected function columns(QueryBuilder $query, $columns = [])
    {
        if (! $columns){
            $columns = ['*'];
        }
        $select = "select ";
        if ($query->distinct) {
            $select = "select distinct";
        }
        return $select . implode(',',$columns);
    }

    protected function from(QueryBuilder $query, $form)
    {
        return ' from '. $form;
    }

    protected function joins()
    {

    }

    protected function wheres(QueryBuilder $queryBuilder, array $wheres = [])
    {
        if (!$wheres){
            return '';
        }

        $wheres_arr = [];
        foreach ($wheres as $index => $where) {
            if (!$index) {
                $where['joiner'] = ' where';
            }
            $wheres_arr[] = sprintf('%s `%s` %s ?', $where['joiner'], $where['column'], $where['operator']);
        }

        return implode($wheres_arr);
    }

    protected function groups()
    {

    }

    protected function orders()
    {

    }

    protected function havings()
    {

    }

    protected function limit()
    {

    }

    protected function offset()
    {

    }

    protected function lock()
    {

    }

}