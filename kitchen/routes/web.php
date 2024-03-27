<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/recipes', 'KitchenController@getRecipes');
$router->post('/empty-order', 'KitchenController@createEmptyOrder');
$router->get('/orders', 'KitchenController@getOrders');
$router->get('/orders/{id}', 'KitchenController@getOrder');
$router->get('/random-recipes', 'KitchenController@getRandomRecipes');
