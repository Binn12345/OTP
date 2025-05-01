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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/verify-otp', 'OtpController@showForm')->name('otp.form');
Route::post('/verify-otp', 'OtpController@verify')->name('otp.verify');


Route::post('/resend-otp', 'OtpController@resend')->name('otp.resend');



