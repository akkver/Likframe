<?php

namespace core\request;

interface RequestInterface
{
    public function __construct($uri, $method, $headers); //初始化

    public static function create($uri, $method, $headers); // 创建request对象

    public function getUri();

    public function getMethod();

    public function getHeader();

}