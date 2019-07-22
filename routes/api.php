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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');


Route::resource('vacancies', 'VacancyAPIController');
Route::post('vacancies/{vacancies}', 'VacancyAPIController@update');
Route::get('vacancies/deactivate/{vacancies}', 'VacancyAPIController@deactivate');

Route::resource('candidates', 'CandidateAPIController');
Route::post('candidates/{candidates}', 'CandidateAPIController@update');
Route::get('candidates/deactivate/{candidates}', 'CandidateAPIController@deactivate');
Route::post('candidates/set-tags/{candidates}', 'CandidateAPIController@set_tags');

Route::resource('comments', 'CommentAPIController');
Route::post('comments/{comments}', 'CommentAPIController@update');
//
//Route::get('test', function(){
//    $user = \App\User::find(1);
//    $user->notify(new \App\Notifications\CandidateShared);
//});






Route::resource('tags', 'TagAPIController');