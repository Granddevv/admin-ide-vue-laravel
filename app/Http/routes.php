<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::group(['domain' => '{user}.ug_laravel_vue.dev'], function ($user) {
	Route::get('/', function ($user) {
		return 'hola '.$user;
	});
});

Route::resource('/', 'HomeController');

//Route::resource('menu2', 'MenusController');


Route::group(['middleware' => 'auth'], function(){

	Route::group(['prefix' => 'api'], function(){

		Route::resource('users', 'UserController');

		Route::resource('menu', 'MenusController');

		Route::resource('rol', 'RolController');

		Route::resource('catalogos', 'CatalogosController');

	});

});




