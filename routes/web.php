<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Purchase Requests
Route::get('/purchase-requests', 'PurchaseRequestController@index')->name('purchase-requests');
Route::get('/purchase-requests/data', 'PurchaseRequestController@data')->name('purchase-requests-data');
Route::post('/purchase-requests/update', 'PurchaseRequestController@update')->name('purchase-requests-update');

//Projects
Route::get('/projects', 'ProjectController@index')->name('projects');
Route::get('/projects/data', 'ProjectController@data')->name('projects-data');
Route::post('/projects/update', 'ProjectController@update')->name('projects-update');
//Projects
Route::get('/tasks/data', 'TaskController@data')->name('tasks-data');
Route::post('/tasks/update', 'TaskController@update')->name('tasks-update');

