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

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ResetPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');

    });

    $api->group(['middleware' => ['jwt.auth', 'isUserActivated']], function(Router $api) {
        $api->post('auth/logout', 'App\\Api\\V1\\Controllers\\LogoutController@logout');
        $api->post('auth/refresh', 'App\\Api\\V1\\Controllers\\RefreshController@refresh');

        # users
        $api->get('me', 'App\\Api\\V1\\Controllers\\UserController@me');
        $api->put('users', 'App\\Api\\V1\\Controllers\\UserController@update');

        # API
        $api->get('articles', 'App\\Api\\V1\\Controllers\\ApiController@index');

        # settings
        $api->get('user-authors', 'App\\Api\\V1\\Controllers\\UserAuthorController@index');
        $api->post('user-authors', 'App\\Api\\V1\\Controllers\\UserAuthorController@store');
        $api->delete('user-authors/{id}', 'App\\Api\\V1\\Controllers\\UserAuthorController@destroy');
        $api->get('user-sources', 'App\\Api\\V1\\Controllers\\UserSourceController@index');
        $api->post('user-sources', 'App\\Api\\V1\\Controllers\\UserSourceController@store');
        $api->delete('user-sources/{id}', 'App\\Api\\V1\\Controllers\\UserSourceController@destroy');
        $api->get('user-categories', 'App\\Api\\V1\\Controllers\\UserCategoryController@index');
        $api->post('user-categories', 'App\\Api\\V1\\Controllers\\UserCategoryController@store');
        $api->delete('user-categories/{id}', 'App\\Api\\V1\\Controllers\\UserCategoryController@destroy');

        # Categories and sources and authors
        $api->get('sources', 'App\\Api\\V1\\Controllers\\SourceController@getAllSources');
        $api->get('categories', 'App\\Api\\V1\\Controllers\\CategoryController@getAllCategories');
        $api->get('authors', 'App\\Api\\V1\\Controllers\\AuthorController@getAllAuthors');
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});
