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

$router->get('/random-recipe', 'GatewayController@getRandomRecipes');
$router->get('/recipes', 'GatewayController@getRecipes');
$router->get('/orders', 'GatewayController@getOrders');
$router->get('/orders/{id}', 'GatewayController@getOrder');
$router->get('/purchases', 'GatewayController@getPurchases');
$router->get('/ingredients', 'GatewayController@getIngredients');
$router->post('/orders', 'GatewayController@createOrder');
