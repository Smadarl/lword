<?php

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'HomeController@index')->name('base');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/games', 'GameController@index')->name('games');
// Route::get('game/{id}/moves', 'GameController@moves');
// Route::get('game/{id}/info', 'GameController@info');
// Route::get('game/{id}/letters', 'GameController@getLetters');
Route::get('/test', 'GameController@test')->name('test');
Route::post('/test', 'Gamecontroller@testPost');

Route::view('user', 'user.index')->name('userProfile');

// TODO: This needs to be built:
Route::get('user/friends', 'UserController@friends');

Route::get('game/create', 'GameController@new');
Route::resource('/game', 'GameController', ['only' => ['show', 'store']]);
Route::get('game/{id}/pending', 'GameController@pending');

Route::get('connect/create', function() { return view('connect.new'); });

// Route::post('game/{id}/letters', 'GameController@saveLetters');


Route::get('api/game/{id}', 'GameController@gameData');
Route::get('api/game/{id}/update', 'GameController@gameData');
Route::post('api/game/create', 'GameController@create');
Route::post('api/game/{id}/letters', 'GameController@saveLetters');
Route::post('api/game/{id}/move', 'GameController@store');
Route::post('api/game/{id}/cancel', 'GameController@cancel');
Route::post('api/game/{id}/accept', 'GameController@accept');

Route::get('api/user/info', 'UserController@info');
Route::get('api/user/requests', 'UserController@requests');
Route::post('api/user/changepw', 'UserController@changepw');
Route::get('api/user/friends', 'UserController@apiFriends');
Route::post('api/user/friend', 'UserController@addFriend');
Route::post('api/user/friend_respond', 'UserController@friendRespond');
Route::get('api/friend/{id}/games', 'UserController@friendGames');


// TODO: fix these
// Route::post('api/game/{id}/move', 'GameController@saveMove');
// Route::post('api/game/create', 'GameController@create');
