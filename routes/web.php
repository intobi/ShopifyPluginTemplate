<?php



//shopify
Route::get('/',                     'Controller@index')                     ->name('home');
Route::get('/install',              'ShopifyApiController@install')         ->name('shopify.install');
Route::get('/auth/shopify/callback','ShopifyApiController@callback')        ->name('shopify.auth.callback');
Route::get('/error',                'ShopifyApiController@error')           ->name('shopify.error');
Route::get('/activate',             'ShopifyApiController@activateCharge')  ->name('shopify.activate');
Route::get('/shop',                 'ShopifyApiController@getShopData')     ->name('shopify.shop.data');

Route::get('/admin',                'Controller@adminLogin')                ->name('adminLogin');

//shopify webhooks
Route::post('/webhook/app/uninstalled', 'ShopifyWebhookController@appUninstalled')->name('shopify.app.uninstalled');
Route::post('/webhook/shop/updated',    'ShopifyWebhookController@shopUpdated')   ->name('shopify.shop.updated');

Route::group(['namespace' => 'ShopifyOwner'], function () {

    Route::get('/app',              'HomeController@index')                 ->name('backend.index');
    Route::get('/logout',           'ShopifyOwnerController@logout')        ->name('backend.logout');

});

Route::group(['namespace' => 'Admin', 'prefix'=>'admin'], function () {

    Route::get('/dashboard',        'HomeController@dashboard')             ->name('admin.dashboard');
    Route::get('/logout',           'HomeController@logout')                ->name('admin.logout');

});


Auth::routes();