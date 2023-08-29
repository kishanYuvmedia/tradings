<?php
use Illuminate\Support\Facades\Route;
Route::get('/users', 'Api\Frontend\PublicApiController@users');
Route::get('getintraday/{type}', 'Api\Frontend\PublicApiController@getintraday');
