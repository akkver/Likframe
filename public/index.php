<?php


require __DIR__.'/../vendor/autoload.php';

// spl_autoload_register(function ($class){
//     // $class => App\User
//     $psr4 = [
//         "App" => "app"
//     ];
//     // 后缀
//     $suffix = ".php";
//     foreach($psr4 as $name => $value) {
//         $class = str_replace($name, $value, $class);
//     }
//     // class 为app\User
//     include($class . $suffix);
// });

require_once __DIR__.'/../app.php';


// App::getContainer()->bind('str', function (){
//     return 'hello str';
// });
//
// echo App::getContainer()->get('str');
//
// (new App\User())->php();

// hello();


// 绑定request
App::getContainer()->bind(\core\request\RequestInterface::class, function (){
    return \core\request\PhpRequest::create(
        $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER
    );
});

// echo App::getContainer()->get(RequestInterface::class)->getMethod();
// var_dump(App::getContainer()->get(RequestInterface::class)->getHeader());

App::getContainer()->get('response')->setContent(
    App::getContainer()->get('router')->dispatch(
        App::getContainer()->get(\core\request\RequestInterface::class)
    )
)->send();

