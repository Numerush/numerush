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

Route::get('/policy', function () {
    return view('policy');
});

Route::get('/user/verify/{token}', 'Api\Auth\RegisterController@verifyUser');

Route::get('/admin-login', 'Admin\AdminLoginController@showLoginForm');
Route::post('/admin-submit', 'Admin\AdminLoginController@login');

Route::group(['middleware' => 'auth:admin'], function () {
    Route::get('/admin/home', 'Admin\AdminController@index');

    Route::get('/crud/kategori', 'Admin\KategoriController@index');
    Route::get('/crud/kurir', 'Admin\KurirController@index');
    Route::get('/crud/kota', 'Admin\KotaController@index');
    Route::get('/crud/negara', 'Admin\NegaraController@index');
    Route::get('/crud/status_transaksi', 'Admin\StatusController@index');
    
    Route::get('/admin/verify/transaction', 'Admin\TransactionController@index');
    Route::post('/admin/verified/transaction', 'Admin\TransactionController@verified');
});
Route::post('login/auth/google', 'Api\Auth\SocialLoginController@googleAuth');
Route::get('login/social/facebook', 'Api\Auth\SocialLoginController@redirectToProvider');
Route::get('login/callback/facebook', 'Api\Auth\SocialLoginController@handleProviderCallback');
