<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/games', 'GameController@index')->name('games');
Route::get('game/{id}/moves', 'GameController@moves');
Route::get('game/{id}/info', 'GameController@info');
Route::get('user/friends', 'UserController@friends');
Route::get('game/{id}/letters', 'GameController@getLetters');

Route::view('user', 'user.index')->name('userProfile');

Route::resource('/game', 'GameController', ['only' => ['show', 'store']]);

Route::post('game/create', 'GameController@create');
Route::post('game/{id}/letters', 'GameController@saveLetters');





Route::get('api/game/{id}', 'GameController@gameData');
Route::post('api/game/{id}/letters', 'GameController@saveLetters');
Route::post('api/game/{id}/move', 'GameController@store');

Route::get('api/user/info', 'UserController@info');
Route::get('api/user/requests', 'UserController@requests');
Route::post('api/user/changepw', 'UserController@changepw');
Route::post('api/user/friend', 'UserController@addFriend');
Route::post('api/user/friend_respond', 'UserController@friendRespond');


// TODO: fix these
Route::post('api/game/{id}/move', 'GameController@saveMove');
Route::post('api/game/create', 'GameController@create');
