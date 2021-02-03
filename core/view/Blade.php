<?php declare(strict_types=1);


namespace core\view;


use duncan3dc\Laravel\BladeInstance;

class Blade implements ViewInterface
{
    protected ?object $template = null;

    public function init()
    {
        // 获取配置
        $config = \App::getContainer()->get('config')->get('view');

        // 设置模板配置
        // 设置视图路径 和 缓存路径
        // 用法见: duncan3dc/blade
        $this->template = new BladeInstance($config['view_path'], $config['cache_path']);
        // $this->template->render();
    }

    public function render($path, $params = [])
    {
        return $this->template->render($path, $params);
    }
}