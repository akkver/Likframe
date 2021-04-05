<?php


namespace core\log\driver;

use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class StackLogger extends AbstractLogger
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @example 代码: app('log')->info('{language} is the best language in the world’,['language' => 'php'])
     * @example 返回: php is the best language in the world
     * @example 说白了 就是替换而已
     * @param mixed $message 原本的消息
     * @param array $context 要替换的上下文
     * @return string
     */
    public function interpolate($message, array $context = [])
    {
        //构建一个键名包含花括号的替换数组
        $replace = [];
        foreach ($context as $key => $value) {
            // 检查该值是否可以转换为字符串
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replace['{'.$key.'}'] = $value;
            }
        }
        // 替换记录中的占位符,返回修改后的记录信息
        return strtr($message, $replace);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        if (is_array($message)) {
            $message = var_export($message, true) . var_export($context, true);
        }elseif (is_string($message)) {
            $message = $this->interpolate($message, $context);
        }
        // 根据配置文件格式化
        $message = sprintf($this->config['format'],date('Y-m-d H:i:s'),$level,$message);

        error_log($message.PHP_EOL, 3, $this->config['path'].'/Likframe.log');
    }
}