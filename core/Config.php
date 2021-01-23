<?php declare(strict_types=1);


namespace core;


class Config
{
    protected array $config = [];

    public function init()
    {
        foreach (glob(FRAME_BASE_PATH.'/config/*.php') as $file) {
            $key = str_replace('.php','',basename($file));
            $this->config[$key] = require "$file";
        }
    }

    public function get($key)
    {
        $keys = explode('.',$key);
        $config = $this->config;

        // 这里为什么要用config 不用 this->config
        // 因为config会随着foreach每层改变 而 this->config 不会
        // 最终的config 会确定返回
        foreach ($keys as $key) {
            $config = $config[$key];
        }
        return $config;
    }

    public function set($file, $val)
    {
        $keys = explode('.', $file);
        $newConfig = &$this->config;
        foreach ($keys as $key){
            $newConfig = &$newConfig[$key];
        }
        $newConfig = $val;
    }
}