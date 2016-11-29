<?php


use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;


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
