<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $emails
 */
class Shop extends Model
{
    protected $table = 'shops';

    protected $fillable = [
        'domain',
        'shop_url',
        'access_token',
        'full_name',
        'company_name',
        'email',
        'currency',
        'money_format',
        'trial_days',
        'app_installed',
        'installed_times',
        'charge_id',
        'charge_status',
        'status',
    ];

}
