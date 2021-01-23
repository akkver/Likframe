<?php declare(strict_types=1);


namespace App\middleware;


class WebMiddleWare
{
    public function handle($request, \Closure $next)
    {
        // echo 'web middleware handle';
        return $next($request);
    }
}