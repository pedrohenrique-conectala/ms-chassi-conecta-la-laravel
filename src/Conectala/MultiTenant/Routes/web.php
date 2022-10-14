<?php

/** @var Router $router */

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

use Laravel\Lumen\Routing\Router;

$router->group(['prefix' => 'ms_creating_replace'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    $router->group(['prefix' => '{tenant}/api', 'middleware' => 'Conectala\MultiTenant\Migration\Middleware\TenantConnection', 'namespace' => 'API'], function () use ($router) {
        $router->group(['prefix' => '{store}/v1', 'namespace' => 'v1'], function () use ($router) {
            //$router->post('', 'ExampleController@post');
        });

        $router->group(['prefix' => 'setting'], function () use ($router) {
            $router->post('create', 'SettingController@create');
            $router->get('{param}', 'SettingController@get');
            $router->get('', 'SettingController@get');
            $router->put('{param}', 'SettingController@update');
        });
    });
});

