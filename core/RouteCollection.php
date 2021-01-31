<?php declare(strict_types=1);


namespace core;


use core\request\RequestInterface;

class RouteCollection
{
    protected array $routes = []; // 所有路由
    protected string $routeIndex = ''; // 当前访问路由
    protected array $currGroup = []; // 当前分组

    public function getRoutes()
    {
        return $this->routes;
    }

    public function group($attributes = [], \Closure $callback)
    {
        $this->currGroup[] = $attributes;

        call_user_func($callback, $this);
        // $callback($this); 这个效果一样
        // group主要是实现this， this将当前状态传递到了闭包
        array_pop($this->currGroup);
    }

    // 增加/  如: GETUSER 改成 GET/USER
    protected function addSlash(& $uri)
    {
        return $uri['0'] == '/' ?: $uri = '/'.$uri;
    }

    public function addRoute($method, $uri, $uses)
    {
        $prefix = ''; // 前缀
        $middleware = []; // 中间件
        $namespace = ''; // 命名空间
        $this->addSlash($uri);

        foreach ($this->currGroup as $group) {
            $prefix .= $group['prefix'] ?? '';
            if ($prefix) {
                $this->addSlash($prefix);
            }
            $middleware = $group['middleware'] ?? [];
            $namespace .= $group['namespace'] ?? '';
        }

        $method = strtoupper($method);

        $uri = $prefix . $uri;
        $this->routeIndex = $method.$uri; // 路由索引
        // 路由存储结构  用 GET/USER   这种方式做索引 一次性就找到了
        $this->routes[$this->routeIndex] = [
            'method' => $method,
            'uri' => $uri,
            'action' => [
                'uses' => $uses,
                'middleware' => $middleware,
                'namespace' => $namespace
            ]
        ];
    }

    public function get($uri, $uses)
    {
        $this->addRoute('get', $uri, $uses);
        return $this;
    }

    public function post($uri, $uses)
    {
        $this->addRoute('post', $uri, $uses);
        return $this;
    }

    public function put($uri, $uses)
    {
        $this->addRoute('put', $uri, $uses);
        return $this;
    }

    public function delete($uri, $uses)
    {
        $this->addRoute('delete', $uri, $uses);
        return $this;
    }

    public function middleware($class)
    {
        $this->routes[$this->routeIndex]['action']['middleware'][] = $class;
        return $this;
    }

    // 获取当前访问的路由
    public function getCurrRoute()
    {
        $routes = $this->getRoutes();
        $routeIndex = $this->routeIndex;

        if (isset($routes[$routeIndex])) {
            return $routes[$routeIndex];
        }

        $routeIndex .= '/';

        if (isset($routes[$routeIndex])) {
            return $routes[$routeIndex];
        }

        return false;
    }

    // 根据request执行路由
    public function dispatch(RequestInterface $request)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $this->routeIndex = $method . $uri;

        $route = $this->getCurrRoute();
        if (!$route) {
            return 404;
        }
        $middleware = $route['action']['middleware'] ?? [];
        $routeDispatch = $route['action']['uses'];

        // 非闭包  就是控制器controller了
        if (!$route['action']['uses'] instanceof \Closure) {
            $action = $route['action'];
            $uses = $action['uses'];
            $uses = explode('@', $uses);
            $controller = $action['namespace']. '\\'. $uses['0'];
            $method = $uses['1'];
            $controllerInstance = new $controller;
            // 合并控制器中间件
            $middleware = array_merge($middleware, $controllerInstance->getMiddleware());
            $routeDispatch = function ($request) use ($route, $controllerInstance, $method) {
                return $controllerInstance->callAction($method, [$request]);
            };
        }
        return \App::getContainer()->get('pipeline')->create()->setClass(
            $middleware
        )->run($routeDispatch)($request);

    }


}