<?php

use Illuminate\Support\Facades\Route;

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
Route::get('blog/list', 'BlogsController@index')->name('blog.list');
Route::post('blog/list/table', 'BlogsController@getJsonData')->name('blog.list.table');
Route::middleware('auth')->group(function () {
    Route::get('blog/delete/{id}', 'BlogsController@delete')->name('blog.delete');
    Route::resource('blog', 'BlogsController', ['names' => ['index' => 'blog']]);
    Route::post('blog/table', 'BlogsController@getJsonData')->name('blog.table');
});