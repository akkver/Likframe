<?php


namespace core\log;


use core\log\driver\StackLogger;

class Logger
{
    // 所有实例的通道 就是多例
    protected array $channels = [];

    protected array $config = [];

    public function __construct()
    {
        $this->config = \App::getContainer()->get('config')->get('log');
    }

    public function channel($name = null)
    {
        if (!$name){
            $name = $this->config['default'];
        }
        if (isset($this->channels[$name])) {
            return $this->channels[$name];
        }

        $config = \App::getContainer()->get('config')->get('log.channels.'.$name);

        return $this->channels['name'] = $this->{'create'.ucfirst($config['driver'])}($config);
    }

    public function createStack($config)
    {
        return new StackLogger($config);
    }

    public function __call($name, $arguments)
    {
        return $this->channel()->$name(...$arguments);
    }
}