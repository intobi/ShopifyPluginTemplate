<?php


Route::namespace('ShopifyOwner')->group(function () {
    Route::get('/app', ['as' => 'shopifyowner.home', 'uses' =>  function () {
        return 1;
    }]);
});