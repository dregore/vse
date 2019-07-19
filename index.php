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
$router->post('/api/orders/add', function() {
    $home = new \App\Controllers\HomeController();
    return $home->addOrder();
});

//Update order
$router->put('/api/orders/pay/{d}', function($orderId) {
    $home = new \App\Controllers\HomeController();
    return $home->payOrder($orderId);
});

$router->run();
