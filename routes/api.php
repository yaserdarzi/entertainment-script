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
if (App::environment() == "production")
    \URL::forceScheme('https');

Route::namespace('Api\V1')->prefix('/v1')->group(function () {

//    //Site After login
//    Route::middleware('app.api.check')->namespace('Api')->group(function () {
//
//        //Hotel
//        Route::get('/hotel', 'HotelController@index');
//        Route::get('/hotel/{hotel_id}', 'HotelController@show');
//
//        //Reservation
//        Route::get('/reservation', 'ReservationController@index');
//
//        //Payment
//        Route::post('/payment', 'PaymentController@store');
//
//        //Get Ticket
//        Route::get('/ticket', 'TicketController@index');
//
//    });


    //Supplier
    Route::middleware('app.supplier.check')->namespace('Supplier')->prefix('/supplier')->group(function () {

        //Product
        Route::post('/product/update/{product_id}', 'ProductController@update');
        Route::resource('/product', 'ProductController');

        //Product Comment
        Route::post('/product/{product_id}/comment/update/{product_comment_id}', 'ProductCommentController@update');
        Route::resource('/product/{product_id}/comment', 'ProductCommentController');

        //Product Gallery
        Route::resource('/product/{product_id}/gallery', 'ProductGalleryController');

        //Product Video
        Route::resource('/product/{product_id}/video', 'ProductVideoController');

        //Product Episode
        Route::post('/product/{product_id}/episode/update/{product_episode_id}', 'ProductEpisodeController@update');
        Route::resource('/product/{product_id}/episode', 'ProductEpisodeController');

//        //Setting
//        Route::get('/setting', 'SettingController@index');

        //Supplier Webservice
        Route::namespace('WebService')->prefix('/webservice')->group(function () {

            //Product
            Route::get('product/{product_id}', 'ProductController@show');

            //Product Gallery
            Route::get('/product/{hotel_id}/gallery', 'ProductGalleryController@index');

            //Product Video
            Route::get('/product/{product_id}/video', 'ProductVideoController@index');

            //Reservation
            Route::get('/reservation', 'ReservationController@index');
            Route::get('/reservation/{product_id}', 'ReservationController@show');

            //Payment
            Route::Post('/payment', 'ShoppingController@store');

            //Agency Request
            Route::Post('/agency/request', 'AgencyRequestController@store');

            //Product Comment
            Route::get('/product/{hotel_id}/comment', 'ProductCommentController@index');
        });

    });

    //Agency
    Route::middleware('app.agency.check')->namespace('Agency')->prefix('/agency')->group(function () {

        //Reservation
        Route::get('/reservation', 'ReservationController@index');


    });

});