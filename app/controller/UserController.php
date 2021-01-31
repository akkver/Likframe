<?php


namespace App\controller;


use App\middleware\ControllerMiddleware;
use core\Controller;
use core\request\RequestInterface;

class UserController extends Controller
{
    protected array $middleware = [
        ControllerMiddleware::class,
    ];

    public function index(RequestInterface $request)
    {
        return [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
        ];
    }
}