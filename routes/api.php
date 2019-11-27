<?php

use Illuminate\Http\Request;

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



Route::post('/crawl','KaraokeController@crawlSave');
Route::delete('/karaoke/destroyAll','KaraokeController@DestroyAll');
Route::get('/karaoke/rating/{city}','KaraokeController@rating');
Route::get('/karaoke/getAll/{city}','KaraokeController@getAll');
Route::get('/listProvinces','KaraokeController@listProvinces');
Route::get('/karaoke/testAll','KaraokeController@testAll');
Route::get('/karaoke/{city}','KaraokeController@indexCity');
Route::get('/karaoke/show/{id}','KaraokeController@show');
Route::get('/get','KaraokeController@get');



Route::resource('/karaoke','KaraokeController');

