<?php

namespace App\Http\Controllers\ShopifyOwner;

use Illuminate\Http\Request;
use App\Http\Controllers\ShopifyOwner\ShopifyOwnerController as Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('shopifyowner.index');
    }

}
