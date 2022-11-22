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

use Illuminate\Support\Facades\Route;

$router->group(['namespace' => 'Api\User', 'prefix' => 'api/user'], function () use ($router) {
    // Auth routes
    $router->post('register', 'RegisterController@register');
    $router->post('sign-in', 'SignInController@signIn');

    // Password reset routes
    $router->post('recover-password', ['as' => 'password.reset', 'uses' => 'PasswordController@sendResetLinkEmail']);
    $router->patch('recover-password', 'PasswordController@reset');

    // Company routes
    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->get('companies', 'CompanyController@index');
        $router->post('companies', 'CompanyController@create');
    });
});