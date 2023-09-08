<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User([
            [
                'name' => 'Root',
                'username' => 'root',
                'password' => bcrypt("123456"),
            ]
        ]);
        $user->save();
    }
}
