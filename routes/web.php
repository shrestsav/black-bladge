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

Route::get('/phpinfo',function(){
	return dd(phpinfo());
});

Route::get('/update',function(){
	$orders = App\Order::all();

	foreach($orders as $order){
		if($order->type==2){
			$order->update([
				'drop_timestamp' => \Carbon\Carbon::parse($order['pick_timestamp'])->addHours($order['booked_hours'])
			]);
		}
		if($order->type==1){
			$order->update([
				'pick_timestamp' => \Carbon\Carbon::parse($order['created_at'])->timezone(config('settings.timezone'))->toDateTimeString()
			]);
		}
	}

	$del = App\Order::onlyTrashed()->forceDelete();

	return 'done';
});

Route::group(['prefix' => 'test'], function() {
	Route::get('/notification/{id}','TestController@notification');
	Route::get('/mail','TestController@mail');
	Route::get('/random','TestController@random');
});

Route::get('/', 'HomeController@index')->name('dashboard');

Auth::routes();

Route::middleware(['auth'])->group(function () {
	Route::group(['prefix' => 'admin', 'middleware' => ['role:superAdmin']], function() {
	    Route::resource('roles','RoleController');
	    Route::resource('users','UserController');
	});

	//Driver Routes
	Route::apiResource('/drivers','DriverController');
	Route::get('/driver/all','DriverController@allDrivers');

	//Customer Routes
	Route::apiResource('/customers','CustomerController');
	Route::get('/unverifiedCustomers','CustomerController@unverifiedCustomers');
	Route::post('/deleteCustomers','CustomerController@deleteCustomers');
	Route::get('/address/{customer_id}','CustomerController@address');
	Route::get('/customer/all','CustomerController@all');

    //Vehicle Routes
    Route::get('/vehicle/all','VehicleController@getAllVehicles');
	Route::apiResource('/vehicles','VehicleController');

	//REPORT GENERATION
	Route::group(['prefix' => 'reports'], function() {
		Route::get('/driver/orders/{driver_id}','ReportController@driverOrders');
		Route::get('/customer/orders/{customer_id}','ReportController@customerOrders');

	    // Route::post('/totalOrders','ReportController@totalOrders');
	    // Route::post('/totalCustomers','ReportController@totalCustomers');
	    // Route::post('/totalSales','ReportController@totalSales');
	    // Route::get('/topCustomers','ReportController@topCustomers');
	    // //Exports
	    // Route::get('/export','ReportController@export');
	});

	// Banners
	Route::get('/offers','CoreController@offers');
	Route::post('/offers','CoreController@addOffer');
	Route::post('/offers/edit/{id}','CoreController@editOffer');
	Route::post('/changeOfferStatus','CoreController@changeOfferStatus');
	Route::delete('/offers/{id}','CoreController@deleteOffer');

	//App Defaults
	Route::get('/appDefaults','CoreController@appDefaults');
	Route::post('/appDefaults','CoreController@updateAppDefaults');

	Route::get('/authUser','CoreController@authUser');

	//Booking Routes
	Route::resource('/orders','OrderController');
	Route::get('/getOrders/{status}','OrderController@getOrders');
	Route::get('/getOrdersCount','OrderController@getOrdersCount');
	Route::get('/orders/count/indStatus','OrderController@getIndividualOrdersCount');
	Route::post('/assignOrder','OrderController@assignOrder');
	Route::post('/orders/search/{status}','OrderController@searchOrders');
	Route::post('/cancelMultipleOrders','OrderController@cancelMultipleOrders');
	Route::post('/deleteMultipleOrders','OrderController@deleteMultipleOrders');
	Route::get('/filterCraps','OrderController@filterCraps');

	//Coupon Routes
	Route::apiResource('/coupons','CouponController');
	Route::get('/coupon/referral','CouponController@referralCoupons');
	Route::get('/coupon/orders/{code}','CouponController@redeemedOrders');

	//Route to forward to vue
	Route::get('/v/{any}', 'HomeController@index')->where('any', '.*');

	// Notification Routes
	Route::get('/notifications','UserController@notifications');
	Route::get('/markAsRead/{notificationId}','UserController@markAsRead');
	Route::get('/markAllAsRead','UserController@markAllAsRead');

	//When Pushing Bulk Notifications to devices
	Route::post('/pushNotification','CoreController@sendPushNotification');
	Route::get('/pushNotification','CoreController@sentPushNotification');

	//Config Routes
	Route::get('getFields/{fieldType}','CoreController@getFields');
	Route::get('getSettings/{settingType}','CoreController@getSettings');
	Route::get('orderTime','CoreController@orderTime');

	//PAYPAL INTEGRATION and TESTING
	Route::get('/payment',function(){
		return view('test');
	});
	Route::post('/paypal/initiate','PaypalController@createPayment')->name('createPayment');
	Route::get('/paypal/execute/{order_id}','PaypalController@executePayment')->name('executePayment');
	Route::get('/paypal/retrieve/{paymentID}','PaypalController@retrievePayment');


	//Firestore Tests
	Route::get('firestore','FirestoreController@index');
	Route::get('set','FirestoreController@setData');
	Route::get('whereData','FirestoreController@whereData');

	Route::get('event',function(){
		event(new TaskEvent('Hey how are you'));
	});

});
