<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain')->unique();
            $table->string('shop_url');
            $table->text('access_token');
            $table->string('full_name');
            $table->string('company_name');
            $table->string('email');
            $table->string('currency');
            $table->string('money_format');
            $table->integer('trial_days')->unsigned();
            $table->boolean('app_installed')->default(0);
            $table->integer('installed_times')->nullable()->default(1);

            $table->integer('charge_id')->unsigned()->nullable();
            $table->string('charge_status')->nullable();
            $table->boolean('status')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
