<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/games', 'GameController@index')->name('games');
Route::resource('/game', 'GameController', ['only' => ['show', 'store']]);
Route::get('game/{id}/moves', 'GameController@moves');
Route::get('game/{id}/info', 'GameController@info');
Route::get('user/friends', 'UserController@friends');

Route::post('/game/move', 'GameController@store');
Route::post('game/create', 'GameController@create');
