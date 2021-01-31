<?php


$router->get('/', function (){
    return '现在这里是web-get->/';
});//->middleware(\App\middleware\WebMiddleWare::class);

$router->get('/hello', function (){
    return '现在这里是web-get->hello';
});

$router->get('/config', function (){
    echo App::getContainer()->get('config')->get('database.connections.mysql_one.driver');
    echo '<br>';
    App::getContainer()->get('config')->set('database.connections.mysql_one.driver', 'mysqli');
    echo App::getContainer()->get('config')->get('database.connections.mysql_one.driver');
});

$router->get('/db', function (){
    $id = 1;
    var_dump(
        App::getContainer()->get('db')->select("SELECT * FROM `user_base` WHERE `uid` = ?", [$id])
    );
});

$router->get('/db2', function (){
    $id = 1;
    var_dump(
        App::getContainer()->get('db')->table('user_base')->where('uid', 10)->get()
    );
});

$router->get('/model', function (){
    $users = \App\User::Where('uid', 1)->orWhere('uid', 2)->get();
    foreach ($users as $user) {
        echo $user->testModel()."<br />";
    }
});

$router->get('/controller', 'UserController@index');

