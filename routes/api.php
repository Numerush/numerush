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

Route::get('coba/route', 'Api\TitipanController@index');

Route::post('register', 'Api\Auth\RegisterController@register');
Route::post('login', 'Api\Auth\LoginController@login');
Route::post('refresh', 'Api\Auth\LoginController@refresh');

//ambil data trip, request, dan preorder
Route::get('get/trip', 'Api\TripController@index');
Route::get('get/requesting', 'Api\RequestingController@index');
Route::get('get/preorder', 'Api\PreorderController@index');

//ambil negara yang tersedia
Route::get('get/negara', 'Api\NegaraController@index');

//ambil kota dan asal negaranya
Route::get('get/kota/rajaongkir', 'Api\KotaController@getKotaRajaOngkir');
Route::get('get/kota', 'Api\KotaController@index');
Route::get('get/kota/{negara}', 'Api\KotaController@getKotaFromNegara');
Route::get('get/search/kota/{kota}', 'Api\KotaController@getSearchKota');

//get newest 3 promo
Route::get('get/newPromo', 'Api\PromoController@get3New');

//ga dipake harusnya sekarang (misscom)
//search result from preorder trip and requesting
// Route::get('get/search_result/{kata}', 'Api\UserController@searchResult');

//mengambil detil dari request preorder dan trip
Route::get('get/requesting/{id}', 'Api\RequestingController@detailProduk');
Route::get('get/preorder/{id}', 'Api\PreorderController@detailProduk');
Route::get('get/trip/{id}', 'Api\TripController@detail');

//show all kategori
Route::get('get/kategori', 'Api\UserController@getKategori');

//get user review
Route::get('get/review/{user_id}', 'Api\ReviewController@getUserReview');

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'Api\UserController@user');
    Route::post('logout', 'Api\Auth\LoginController@logout');
    Route::post('password/change', 'Api\Auth\RegisterController@changePassword');
    Route::post('user/data/change', 'Api\Auth\RegisterController@editUser');

    //ambil ongkir untuk preorder ini
    Route::get('ongkir/preorder/{preorder_id}', 'Api\PreorderController@hitungOngkirPreorder');

    //upload gambar bwt chat
    Route::post('save/image', 'Api\UserController@uploadPicture');

    //change user avatar
    Route::post('change/avatar', 'Api\UserController@changeAvatar');
    Route::post('change/rincian', 'Api\UserController@changeRincian');
    
    //get current user timeline
    Route::get('my/timeline', 'Api\TimelineController@followerTimeline');

    //show all user beside me
    Route::get('get/user', 'Api\UserController@index');
    Route::get('get/harga/preorder/{post_id}', 'Api\PreorderController@hargaKirim');

    //get review untuk user yg login
    Route::get('my/review','Api\ReviewController@myReview');
    //get box titipan milik user yg login
    Route::get('my/box','Api\TitipanController@myBox');

    //get data alamat user yg login
    Route::get('my/alamat','Api\AlamatController@index');
    //save alamat baru untuk user yg login
    Route::post('my/alamat','Api\AlamatController@add');
    //hapus alamat user
    Route::post('delete/alamat','Api\AlamatController@deleteAlamat');
    //get alamat default saya
    Route::get('my/alamat/default', 'Api\AlamatController@getDefaultAlamat');
    //set default alamat
    Route::post('my/alamat/default', 'Api\AlamatController@setDefaultAlamat');

    //get data my wishlist
    Route::get('my/like','Api\LikeController@myWishlist');
    //masukan ke wishlist
    Route::post('like/{tipe}', 'Api\LikeController@saveLiked');

    Route::get('get/kurir', 'Api\KurirController@getKurir');
    Route::get('get/postkurir/{post_id}/{tipe}', 'Api\KurirController@getPostKurir');

    Route::get('default/kurir', 'Api\KurirController@getDefaultKurir');
    Route::post('default/kurir', 'Api\KurirController@setDefaultKurir');

    //return review diri ttg sendiri
    Route::get('get/review', 'Api\UserController@getReview');

    //return follower dari user
    Route::get('get/follower', 'Api\UserController@getFollower');

    //follow user lainnya yang memiliki id {user_id}
    Route::post('follow/{user_id}', 'Api\UserController@follow');    
    //review user lainnya
    Route::post('review/user', 'Api\UserController@review');
    //report post preorder requesting atau trip
    Route::post('report/{tipe}', 'Api\UserController@report');
    //seen post story
    Route::post('seen/post', 'Api\UserController@seenPost');
    
    //post trip baru
    Route::post('post/trip', 'Api\TripController@add');
    //posting requesting baru
    Route::post('post/requesting', 'Api\RequestingController@add');
    //post preorder baru
    Route::post('post/preorder', 'Api\PreorderController@add');

    //get request, preorder dan trip milik saya
    Route::get('my/requesting', 'Api\RequestingController@myRequesting');
    Route::get('my/preorder', 'Api\PreorderController@myPreorder');
    Route::get('my/trip', 'Api\TripController@myTrip');

    //get permintaan request dan preorder dan trip yang kita buat ke orang lain
    Route::get('my/offer/requesting', 'Api\RequestingController@myRequestingOffer');
    Route::get('my/offer/preorder', 'Api\PreorderController@myPreorderOffer');
    Route::get('my/offer/trip', 'Api\TripController@myTripOffer');

    //get permintaan request dan preorder dan trip ke kita
    Route::get('get/offer/requesting', 'Api\UserController@myRequestList');
    Route::get('get/offer/preorder', 'Api\UserController@myPreorderList');
    Route::get('get/offer/trip', 'Api\UserController@myTripList');

    //post permintaan baru request dan preorder dan trip org lain
    Route::post('post/offer/requesting', 'Api\RequestingController@newUserRequest');
    Route::post('post/offer/preorder', 'Api\PreorderController@newUserPreorder');
    Route::post('post/offer/trip', 'Api\TripController@newUserTrip');

    //respon ke permintaan baru
    Route::post('respond/offer/requesting', 'Api\RequestingController@respondUserRequest');
    Route::post('respond/offer/preorder', 'Api\PreorderController@respondUserPreorder');
    Route::post('respond/offer/trip', 'Api\TripController@respondUserTrip');

    //get harga
    Route::post('hitung/ongkir', 'Api\TitipanController@hitungOngkir');
    Route::post('confirm/titipan', 'Api\TitipanController@jadiTitipan');
    Route::post('bayar/titipan', 'Api\TitipanController@updatePembayaran');
    Route::post('kirim/titipan', 'Api\TitipanController@updatePengiriman');

    Route::get('show/user/titipan', 'Api\TitipanController@showUserTitipan');
    Route::get('show/shopper/titipan', 'Api\TitipanController@showShopperTitipan');
});
