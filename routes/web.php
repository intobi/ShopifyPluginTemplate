<?php



//shopify
Route::get('/', ['as' => 'home', 'uses' => 'Controller@index']);
Route::get('/install', ['as' => 'shopify.install', 'uses' => 'ShopifyApiController@install']);
Route::get('/auth/shopify/callback', ['as' => 'shopify.auth.callback', 'uses' => 'ShopifyApiController@callback']);
Route::get('/error', ['as' => 'shopify.error', 'uses' => 'ShopifyApiController@error']);
Route::get('/activate', ['as' => 'shopify.activate', 'uses' => 'ShopifyApiController@activateCharge']);
Route::get('/shop', ['as' => 'shopify.shop.data', 'uses' => 'ShopifyApiController@getShopData']);

//shopify webhooks
Route::post('/webhook/app/uninstalled', ['as' => 'shopify.app.uninstalled', 'uses' => 'ShopifyWebhookController@appUninstalled']);
Route::post('/webhook/shop/updated', ['as' => 'shopify.shop.updated', 'uses' => 'ShopifyWebhookController@shopUpdated']);

Route::namespace('ShopifyOwner')->group(function () {
    Route::get('/app', ['as' => 'backend.index', 'uses' => 'HomeController@index']);
});


Auth::routes();