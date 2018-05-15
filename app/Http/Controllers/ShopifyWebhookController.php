<?php

namespace App\Http\Controllers;


use App\Shop;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\InstallRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopifyWebhookController extends BaseController
{


    /**
     * Shopify webhoo k when app uninstalled
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appUninstalled(Request $request)
    {
//        Log::info('SHURA v2: uninstall  DOMAIN ' .  $request->myshopify_domain);
//
//        $shop = Shop::where('domain', $request->myshopify_domain);
//
//
//        if ($shop->first() !== null) {
//            $shop = $shop->first();
//            DB::table('timers')->where('shop_id', $shop->id)->delete();
//
//            Log::info('SHURA v2: uninstall  STATUS ' . $shop->status);
//
//            if ($shop->status === 0)
//                $shop->delete();
//            else
//                $shop->update([
//                    'app_installed' => 0,
//                    'charge_id' => null,
//                    'charge_status' => null,
//                    'status' => 1
//                ]);
//
//        }
        return response()->json(true);
    }

    /**
     * Shopify webhook when shop settings updated
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopUpdated(Request $request)
    {
//        Shop::where('domain', $request->domain)->update([
//            'full_name' => $request->shop_owner,
//            'company_name' => $request->name,
//            'email' => $request->email,
//            'currency' => $request->currency,
//            'money_format' => $request->money_format
//        ]);

        return response()->json(true);
    }

    /**
     * Return decrypted query param from URL
     *
     * @param string $url
     * @param string $param
     * @return bool|int
     */
    private function getQueryParam($url, $param)
    {
        $parsed_url = parse_url($url);

        if(isset($parsed_url['query'])) {
            $query_str = $parsed_url['query'];
        } else {
            return false;
        }

        parse_str($query_str, $query_arr);

        if(isset($query_arr[$param])) {
            return decrypt($query_arr[$param]);
        }

        return false;
    }


}
