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

Route::get('/', 'VideosController@welcome')->name('welcome');

Route::resource('videos', 'VideosController');

Route::resource('tags', 'TagsController');

Route::post('videos/attach', [
    'as' => 'videos.attach',
    'uses' => 'VideosController@attach'
]);
Route::post('videos/detach', [
    'as' => 'videos.detach',
    'uses' => 'VideosController@detach'
]);

Route::group([ 'prefix' => 'api' ], function() {
    Route::get('videos/search', [
        'as' => 'videos.search',
        'uses' => 'VideosController@search'
    ]);
    Route::get('tags/search', [
        'as' => 'tags.search',
        'uses' => 'TagsController@search'
    ]);
    Route::get('videos/{id}/relatedVideos', 'VideosController@relatedVideos');
});

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
    //
});
