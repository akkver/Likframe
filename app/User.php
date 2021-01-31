<?php

namespace App;

use core\database\model\Model;

class User extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user_base';
    }

    public function php()
    {
        echo "hello php";
    }

    public function testModel()
    {
        return "id = {$this->uid}, name: {$this->name}, say:that is ok!!";
    }

}

