<?php


use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

}


Router::defaultRouteClass('DashedRoute');

Router::scope('/', function (RouteBuilder $routes) {
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'home']);

    $routes->fallbacks('DashedRoute');
});


// Ajout du prefix pour l'admin
Router::prefix('admin', function ($routes) {
    $routes->connect('/', ['controller'=>'Pages','action'=>'statistiques']);

    $routes->fallbacks('DashedRoute');
});

Plugin::routes();
