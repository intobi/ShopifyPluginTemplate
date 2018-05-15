<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallRequest;
use App\Shop;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

//
// https://github.com/joshrps/laravel-shopify-API-wrapper
//
//
class ShopifyApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Install / login Shopify app as ShopifyOwner
     *
     * @param InstallRequest $request
     * @return mixed
     */
    public function install(InstallRequest $request)
    {
        //extra link for different charge trial
//        $request->session()->put('trial', $request->get('slogin'));

        //init connect between shop and app
        //define which  shop (shop domain)  and which app (api key/secret)
        $sh = App::make('ShopifyAPI', [
            'API_KEY'       => env('SHOPIFY_API_KEY'),
            'API_SECRET'    => env('SHOPIFY_API_SECRET'),
            'SHOP_DOMAIN'   => $this->getShopDomain($request->shop),
        ]);

        //define permissions that App will request from user
        //define "return back" point
        $install_url =  $sh->installURL([
            'permissions' => [
                'read_orders',
                'write_orders',
                'read_checkouts',
                'read_products',
                'read_script_tags',
                'write_script_tags'
            ],
            'redirect' => route('shopify.auth.callback')
        ]);
        return redirect()->to($install_url);
    }

    /**
     * "return back" point after install
     *
     * @param Request $request
     * @return mixed
     */
    public function callback(Request $request)
    {

        //init connect between shop and app
        $sh = App::make('ShopifyAPI', [
            'API_KEY'       => env('SHOPIFY_API_KEY'),
            'API_SECRET'    => env('SHOPIFY_API_SECRET'),
            'SHOP_DOMAIN'   => $request->shop
        ]);

        $trial = 14;
        //extra link for different charge trial
//        $check_sum = $request->session()->get('trial');
//                    $request->session()->forget('trial');
//        $trial = ($check_sum === 'OTANCg') ? 360 : $trial;
//        $trial = ($check_sum === 'MTgw') ? 180 : $trial;
//        $trial = ($check_sum === 'MzYw') ? 90 : $trial;

        try {
            //???
            $verify = $sh->verifyRequest($request->all());

            if ($verify) {
                //get access token.
                //is used for access from app to shop
                $access_token = $sh->getAccessToken($request->code);

                //remember user in app.
                $request->session()->put('shop_domain', $request->shop);

                //???
                $sh->setup(['ACCESS_TOKEN' => $access_token]);

                //else return null
                $shop = Shop::where('domain', $request->shop)->first();
                if ($shop) {//if shop exist in db

                     $shop->update([
                        'access_token' => $access_token,
                    ]);

                     //if shop was reinstalled
                    if ($shop->app_installed === 0)
                    {
                        //attach scripts and webhooks for user shop
                        $this->addScripts($sh);
                        $this->createWebhooks($sh);

                        $shop->update(['app_installed' => 1, 'installed_times' => $shop->installed_times + 1 ]);
                    }
                    //charge strategy: user can't access to app, while he don't approve charge (status === 2)
//                    if ($shop->status !== 2 || $trial > $shop->trial_days)
//                        return $this->approveTrial($sh, $shop, $trial);

                } else {//if shop doesn't exist in db
                    $shopify_shop = $sh->call([
                        'METHOD'    => 'GET',
                        'URL'       => '/admin/shop.json'
                    ]);

                    //create shop in db, attach scripts and webhooks for user shop
                    $shop_id =  $this->createTables($shopify_shop->shop, $access_token,  $trial);
                    $this->addScripts($sh);
                    $this->createWebhooks($sh);

//                    return $this->approveTrial($sh, $shop, $trial);    //first install. Redirect to approve charge and activate billing
                }
            } else {
//                flash('Verification Request Failed')->error();
                return redirect()->route('shopify.error');
            }
        } catch (\Exception $e) {
            dd($e);
//            flash($e->getMessage())->error();
            return redirect()->route('shopify.error');
        }
        //redirect after install complete successfully
        return redirect()->route('backend.index');
    }


    /**
     * Subscribe user for trial period. Write charge id to db and redir to charge activate
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public  function approveTrial($sh, $shop, $trial){
        //define bigger trial
        if ($shop->trial_days > $trial)
            $trial = $shop->trial_days;

        //define  remaining time of trial. In this case trial calculated from "created_time" of shop value in db
        $trial_left = ((strtotime($shop->created_at->addDays($trial)) - strtotime(now())) / (60 * 60 * 24));
        $trial_left += (  $trial_left > 0 ) ? 1 : 0;
        $trial_left = (int)$trial_left;

        //charge plan
        $charge_response = $sh->call([
            'METHOD'    => 'POST',
            'URL'       => '/admin/recurring_application_charges.json',
            'DATA'      => ['recurring_application_charge' => [
                'name' => 'The Paid Plan',
                'price' => 7.95,
                'return_url' => route('shopify.activate'),
                'test' => null,
                'terms' => '$7.95 per month, recurring charge',
                'trial_days' => (  $trial_left > 0 ) ? $trial_left : 0,
            ]]
        ]);


        $shop->update(['charge_id' => $charge_response->recurring_application_charge->id]);
        return redirect($charge_response->recurring_application_charge->confirmation_url);
    }


    /**
     * Redirect to form of approving billing. Write to Db if charge activated and remove charge_id if disabled.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateCharge(Request $request)
    {
        $shop = Shop::where('charge_id', $request->charge_id)->first();

        $sh = App::make('ShopifyAPI', [
            'API_KEY'       => env('SHOPIFY_API_KEY'),
            'API_SECRET'    => env('SHOPIFY_API_SECRET'),
            'SHOP_DOMAIN'   => $shop->domain,
            'ACCESS_TOKEN'  => $shop->access_token
        ]);

        $charge_response = $sh->call([
            'METHOD'    => 'GET',
            'URL'       => "/admin/recurring_application_charges/$request->charge_id.json"
        ]);

        $charge = $charge_response->recurring_application_charge;

        if ($charge->status == 'accepted') {
            $charge_response = $sh->call([
                'METHOD'    => 'POST',
                'URL'       => "/admin/recurring_application_charges/$charge->id/activate.json",
                'DATA'      => ['recurring_application_charge' => [
                    'id' => $charge->id,
                    'name' => $charge->name,
                    'api_client_id' => $charge->api_client_id,
                    'price' => $charge->price,
                    'status' => $charge->status,
                    'return_url' => $charge->return_url,
                    'billing_on' => $charge->billing_on,
                    'created_at' => $charge->created_at,
                    'updated_at' => $charge->updated_at,
                    'test' => $charge->test,
                    'activated_on' => $charge->activated_on,
                    'trial_ends_on' => $charge->trial_ends_on,
                    'cancelled_on' => $charge->cancelled_on,
                    'trial_days' => $charge->trial_days,
                    'decorated_return_url' => $charge->decorated_return_url
                ]]
            ]);

            $charge = $charge_response->recurring_application_charge;

            $trial_left = (int)((strtotime(now()) - strtotime($shop->created_at)) / (60 * 60  * 24));
            $trial_left += $charge->trial_days;

            if ($charge->status == 'active') {
                $shop->update(['charge_status' => 'active', 'status' => 2, 'trial_days' =>  $trial_left]);
            } else {
                $shop->update(['charge_status' => $charge->status]);
            }
        } else {
            if ($shop->status !== 2) {
                $domain = $shop->domain;
                $shop->update(['charge_id' => null, 'charge_status' => null, 'app_installed' => 0]);
                $sh->call([
                    'METHOD'    => 'DELETE',
                    'URL'       => '/admin/api_permissions/current.json'
                ]);
                if ($shop->status === 0)
                    $shop->delete();
                return Redirect::away('https://' .  $domain . '/admin/apps/');
            }
        }


        return Redirect::away('https://' .  $shop->domain .'/admin/apps/' . env('SHOPIFY_API_KEY') );
//        return redirect()->route('backend.index');
    }

    /**
     * Error page
     *
     * @return mixed
     */
    public function error()
    {
        return view('pages.error');
    }


    /**
     * Create required tables
     *
     * @param object $shopify_shop
     * @param string $access_token
     */
    private function createTables($shopify_shop, $access_token, $trial)
    {
        $shop = Shop::create([
            'domain' => $shopify_shop->myshopify_domain,
            'shop_url' => $this->getShopUrl($shopify_shop->domain),
            'access_token' => $access_token,
            'full_name' => $shopify_shop->shop_owner,
            'company_name' => $shopify_shop->name,
            'email' => $shopify_shop->email,
            'currency' => $shopify_shop->currency,
            'money_format' => $shopify_shop->money_format,
            'status' => 0,
            'app_installed' => 1,
            'installed_times' => 1,
            'trial_days' => $trial
        ]);

        return $shop->id;
    }

    /**
     * Get Shop URL from name
     *
     * @param string $shop_name
     * @return string
     */
    private function getShopUrl($shop_name)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->get("https://$shop_name");

        if($response->getStatusCode() == 200) {
            return "https://$shop_name";
        }

        return "http://$shop_name";
    }

    /**
     * Get Shop name from URL
     *
     * @param string $url
     * @return string
     */
    private function getShopDomain($url)
    {
        if(isset(parse_url($url)['scheme'])){
            return parse_url($url)['host'];
        }

        return parse_url("//".$url)['host'];
    }

    /**
     * Add JavaScript to Shopify
     *
     * @param object $sh
     */
    private function addScripts($sh)
    {
        $sh->call([
            'METHOD' => 'POST',
            'URL' => '/admin/script_tags.json',
            'DATA' => ['script_tag' => [
                'event' => 'onload',
                'src' => url("js/MyShopifyScript.js")
            ]]
        ]);
    }

    /**
     * Create Shopify Webhooks
     *
     * @param object $sh
     */
    private function createWebhooks($sh)
    {
        $sh->call([
            'METHOD'    => 'POST',
            'URL'       => '/admin/webhooks.json',
            'DATA'      => ['webhook' => [
                'topic' => 'app/uninstalled',
                'address' => route('shopify.app.uninstalled'),
                'format' => 'json'
            ]]
        ]);

        $sh->call([
            'METHOD'    => 'POST',
            'URL'       => '/admin/webhooks.json',
            'DATA'      => ['webhook' => [
                'topic' => 'shop/update',
                'address' => route('shopify.shop.updated'),
                'format' => 'json'
            ]]
        ]);

    }

}
