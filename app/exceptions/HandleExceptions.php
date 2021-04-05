<?php


namespace App\exceptions;


use core\HandleExceptions as BaseHandleExceptions;

class HandleExceptions extends BaseHandleExceptions
{
    // 要忽略的异常 不记录到日志去
    protected array $ignore = [
        // this
        ErrorMessageException::class,
    ];
}