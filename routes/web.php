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

Route::get('/room', 'RoomController@showRoomView')->name('room');
Route::get('/', 'RoomController@showCreateView');
Route::post('/into', 'RoomController@intoRoomView');
Route::post('importExcel', 'RoomController@importExcel');
Route::get('importExport', 'RoomController@importExport');
