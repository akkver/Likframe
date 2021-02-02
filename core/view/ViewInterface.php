<?php


namespace core\view;


interface ViewInterface
{
    // 初始化模板
    public function init();

    // 解析模板
    public function render($path);
}
