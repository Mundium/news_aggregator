<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api->version('v1', function (Router $api) {

    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');

    });

    $api->group(['middleware' => ['jwt.auth', 'isUserActivated']], function(Router $api) {
        $api->post('auth/logout', 'App\\Api\\V1\\Controllers\\LogoutController@logout');
        $api->post('auth/refresh', 'App\\Api\\V1\\Controllers\\RefreshController@refresh');

        # users
        $api->get('me', 'App\\Api\\V1\\Controllers\\UserController@me');
        $api->put('users', 'App\\Api\\V1\\Controllers\\UserController@update');

    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});
