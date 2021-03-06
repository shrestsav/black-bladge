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
Route::group(['middleware' => ['localization']], function() {
	Route::post('/customer/login','Api\Auth\AuthController@phoneRegister');
	Route::post('/customer/verifyOTP','Api\Auth\AuthController@verifyOTP');
	Route::post('/driver/login','Api\Auth\AuthController@driverLogin');
	
	Route::middleware('auth:api')->get('/user', function (Request $request) {
		return $request->user();
	});

	Route::group(['namespace' => 'Api', 'middleware' => ['auth:api']], function() {
		Route::get('/checkRole','Auth\AuthController@checkRole');
		Route::apiResource('/orders','OrderController');

		Route::group(['namespace' => 'Customer', 'prefix' => 'customer', 'middleware' => ['role:customer']], function() {
			
			Route::group(['prefix' => 'profile'], function() {
				Route::post('/create','CustomerController@createProfile');
				Route::post('/update','CustomerController@updateProfile');
				Route::get('/details','CustomerController@index');
			});

			Route::group(['prefix' => 'booking'], function() {
				Route::get('/list','BookingController@list');
				Route::post('/create/{type}','BookingController@create');
				Route::get('/details/{booking_id}','BookingController@details');
				Route::get('/active','BookingController@active');
				Route::get('/active/list','BookingController@activeList');
				Route::get('/completed','BookingController@completed');

				Route::post('/cancel/{order_id}','BookingController@cancel');
				Route::get('/cancelled','BookingController@cancelled');

				Route::post('/coupon/check','BookingController@checkCoupon');
			});

			Route::group(['prefix' => 'location'], function() {
				Route::get('/history','LocationController@history');
				Route::post('/history/recent','LocationController@saveRecentHistory');
				Route::post('/history/nearby','LocationController@saveNearbyHistory');
				Route::post('/history/favorite/{history_id}','LocationController@saveFavoriteHistory');
				Route::get('/history/favorite/remove/{fav_id}','LocationController@removeFavorite');
			});

			Route::apiResource('/cards','CardController');
			Route::get('/card/default/{card_id}','CardController@setDefault');
			
			Route::group(['prefix' => 'page'], function() {
				Route::get('/legal','PageController@legalDocs');
				Route::get('/company-contacts','PageController@contact');
			});
		});
		
		Route::group(['namespace' => 'Driver', 'prefix' => 'driver', 'middleware' => ['role:driver']], function() {
			Route::get('/details','DriverController@index');
			Route::get('/vehicle/set/{vehicle_id}','DriverController@setVehicle');
			Route::get('/vehicles','DriverController@vehicles');
			Route::group(['prefix' => 'booking'], function() {
				Route::get('/details/{booking_id}','BookingController@details');
				Route::get('/new','BookingController@new');
				Route::get('/active','BookingController@accepted');
				Route::get('/completed','BookingController@completed');

				Route::get('/accept/{order_id}','BookingController@accept');
				Route::post('/cancel/{order_id}','BookingController@cancel');

				Route::get('/startTripToPickLocation/{order_id}','BookingController@startTripToPickLocation');
				Route::get('/arrivedAtPickLocation/{order_id}','BookingController@arrivedAtPickLocation');
				Route::get('/startTripForDestination/{order_id}','BookingController@startTripForDestination');
				Route::post('/arrivedAtDropLocation/{order_id}','BookingController@arrivedAtDropLocation');
				Route::get('/paymentDone/{order_id}','BookingController@paymentDone');

				Route::post('/add/dropLocation/{order_id}','BookingController@addDropLocation');
				Route::post('/add/time/{order_id}','BookingController@addTime');
			});
		});

		Route::get('/appConfigs','CoreController@appDefaults');
		Route::post('/feedback','FeedbackController@feedback');

		Route::get('/offers','CoreController@offers');

		// Others
		Route::get('/termsAndConditions','CoreController@termsAndConditions');
		Route::get('/FAQS','CoreController@FAQS');

		//Notifications
		Route::group(['namespace' => 'Auth'], function() {
			Route::get('/notifications','AuthController@notifications');
			Route::get('/countUnreadNotifications','AuthController@countUnreadNotifications');
			Route::get('/markAsRead/{notificationID}','AuthController@markAsRead');
			Route::get('/markAllAsRead','AuthController@markAllAsRead');
			Route::get('/clearAllNotifications','AuthController@deleteUserNotifications');

			Route::post('/deviceToken/remove','AuthController@removeDeviceToken');
		});















		Route::get('/test','OrderController@test');
		Route::get('/tokens','AuthController@tokens');
		
		Route::get('/supportInfo','CoreController@supportInfo');
		Route::get('/orderDefaults','CoreController@orderDefaults');
		Route::get('/configs/{configType}','CoreController@getSettings');
		Route::get('/mainAreas','CoreController@mainAreas');
		
		
		Route::get('/orderTypeDesc','CoreController@orderTypeDesc');
		Route::get('/servicesPlusItems','CoreController@servicesPlusItems');
	});
});