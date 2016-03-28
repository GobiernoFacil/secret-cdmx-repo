<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Frontend
Route::get('/', "Frontend@index");

Route::get('v2', "Contracts@index");

Route::get('test', "ContractGetter@getList");

Route::get('test2/{ocid}', "ContractGetter@getMetaData");
Route::get('test3/{from}/{to}', "ContractGetter@getProviders");
Route::get('test4/{from?}', "ContractGetter@saveProviders");
Route::get('test5', "ContractGetter@updateContracts");
Route::get('contratos', "Contracts@index");

// se esconde mientras va en vivo Route::get('contratos', "Contracts@index"); 
Route::get('contrato/{ocid}', "Contracts@show");
Route::get('contrato/json/{ocid}', "Contracts@showRaw");

/*
.......................................
. T H E   A P I   M I D D L E W A R E .
.......................................
*/
Route::get('api/contratos/todos', 'ApiCDMX@listAll');


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
  Route::get('_contratos', 'Contracts@showAll');
});
