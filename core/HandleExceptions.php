<?php


namespace core;


use Throwable;

class HandleExceptions
{
    protected array $ignore = [];

    public function init()
    {
        // 所有的异常都托管到handleException方法
        set_exception_handler([$this, 'handleException']);

        // 所有错误都托管到handleError方法
        set_error_handler([$this, 'handleError']);
    }

    public function handleError($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext)
    {
        $temp = '死机 都死机 自动开机 关机 重启再死机 三星手机 苹果手机 所有都死机 全世界只剩小米.....';
        app('response')->setContent($temp)->setCode(500)->send();

        // 记录到日志
        app('log')->error($errorFile.' in '.$errorLine.' : '.$errorMessage);
    }

    public function handleException(Throwable $throwable)
    {
        // 如果自定义的异常类存在render方法
        if (method_exists($throwable, 'render')) {
            app('response')->setContent($throwable->render())->send();
        }

        // 不忽略的 记录异常到日志去
        if (!$this->isIgnore($throwable)) {
            app('log')->debug($throwable->getFile().' in '.$throwable->getLine().' : '.$throwable->getMessage());

            // 显示给开发者 以便查找错误
            echo $throwable->getFile().' in '.$throwable->getLine().' : '.$throwable->getMessage();
        }
    }

    public function isIgnore(Throwable $throwable)
    {
        foreach ($this->ignore as $value) {
            if ($value == get_class($throwable)) {
                return true;
            }
        }
        return false;
    }
}