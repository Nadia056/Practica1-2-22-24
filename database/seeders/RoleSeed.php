<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\User;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rol::create([
            'name' => 'Administrator'
        ]);
        Rol::create([
            'name' => 'Coordinator'        
        ]);
        Rol::create([
            'name' => 'Guest'
        ]);

        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'nadiasalzr@gmail.com',
        //     'password' => 'nadianadia',
        //     'role_id' => '1',
        //     'admin_code' => '1234',
        //     'is_verified' => '1',
        //     'phone' => '1234567890']);


    }
}
