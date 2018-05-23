<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            'name' => 'qqQQqq',
            'email' => 'qq@mail.com',
            'password' => bcrypt('123'),

        ]);
    }
}
