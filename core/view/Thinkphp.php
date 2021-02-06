<?php


namespace core\view;


use think\Template;

class Thinkphp implements ViewInterface
{

    protected ?object $template = null;

    public function init()
    {
        // 初始化
        $config = \App::getContainer()->get('config')->get('view');

        $this->template = new Template([
            'view_path' => $config['view_path'],
            'cache_path' => $config['cache_path']
        ]);
    }

    public function render($path, array $params = [])
    {
        $this->template->assign($params);
        $path = str_replace('.', '/', $path);
        return $this->template->fetch($path);
    }
}