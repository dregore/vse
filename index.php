<?php
require './vendor/autoload.php';

$router = new Buki\Router();

//Generate dummy data
$router->get('/api/generate', function() {
    $home = new \App\Controllers\HomeController();
    return $home->apiGenerate();
});

//Get all goods
$router->get('/api/goods', function() {
    $home = new \App\Controllers\HomeController();
    return $home->apiGoods();
});

//Place new order with status new
//Goods ids sends in body with key ids comma separated
//Example ids:  1,3,7,13
$router->post('/api/orders/add', function() {
    $home = new \App\Controllers\HomeController();
    return $home->addOrder();
});

//Update order
//Use order id in request url and order sum in body with key total in json
//Example: {"total":5516.60}
$router->put('/api/orders/pay/{d}', function($orderId) {
    $home = new \App\Controllers\HomeController();
    return $home->payOrder($orderId);
});

$router->run();
