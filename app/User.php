<?php

namespace App;

use core\database\model\Model;

class User extends Model
{
    public function php()
    {
        echo "hello php";
    }

    public function testModel()
    {
        return "id = {$this->uid}, name: {$this->name}, say:that is ok!!";
    }

}

