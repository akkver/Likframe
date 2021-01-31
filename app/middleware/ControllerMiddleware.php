<?php


namespace App\middleware;


use core\request\RequestInterface;

class ControllerMiddleware
{

    public function handle(RequestInterface $request, \Closure $closure)
    {
        echo "<hr/>controller middleware<hr/>";
        return $closure($request);
    }
}