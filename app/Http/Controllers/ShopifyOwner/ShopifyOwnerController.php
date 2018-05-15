<?php

namespace App\Http\Controllers\ShopifyOwner;

use App\Shop;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;


class ShopifyOwnerController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $sh;
    public $shop;


    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(!session()->has('shop_domain')) {
                return redirect()->route('home');
            }

            $shop = Shop::where('domain', session('shop_domain'))->first();

            if(!$shop) {
                return redirect()->route('home');
            }

            $this->sh = App::make('ShopifyAPI', [
                'API_KEY' => env('SHOPIFY_API_KEY'),
                'API_SECRET' => env('SHOPIFY_API_SECRET'),
                'SHOP_DOMAIN' => $shop->domain,
                'ACCESS_TOKEN' => $shop->access_token
            ]);

            $this->shop = $shop;
//            if ($shop->app_installed === 0 || $shop->status !== 1)
            if ($shop->app_installed === 0)
                return $this->logout();

            return $next($request);
        });
    }

    public function logout()
    {
        session()->forget('shop_domain');

        return redirect()->route('home');
    }
}
