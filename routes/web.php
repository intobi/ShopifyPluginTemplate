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
Route::post('/webhook/app/uninstalled', ['as' => 'shopify.app.uninstalled', 'uses' => 'ShopifyWebhookController@appUninstalled']);
Route::post('/webhook/shop/updated', ['as' => 'shopify.shop.updated', 'uses' => 'ShopifyWebhookController@shopUpdated']);

Route::group(['namespace' => 'ShopifyOwner'], function () {
    Route::get('/app', 'HomeController@index')->name('backend.index');
});

Route::group(['namespace' => 'Admin', 'prefix'=>'admin'], function () {

    Route::get('/dashboard',    'HomeController@dashboard')     ->name('admin.dashboard');
    Route::get('/logout',       'HomeController@logout')        ->name('admin.logout');

});




Auth::routes();