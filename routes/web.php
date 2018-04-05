<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/games', 'GameController@index')->name('games');
Route::resource('/game', 'GameController', ['only' => ['show', 'store']]);
Route::get('game/{id}/moves', 'GameController@moves');

Route::post('/game/move', 'GameController@store');
