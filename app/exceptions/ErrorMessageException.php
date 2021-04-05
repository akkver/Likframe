<?php


namespace App\exceptions;


use Exception;

class ErrorMessageException extends Exception
{
    public function render()
    {
        return [
            'data' => $this->getMessage(),
            'code' => 400
        ];
    }
}