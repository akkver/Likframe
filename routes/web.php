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
