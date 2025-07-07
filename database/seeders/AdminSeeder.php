<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'name' => 'admin',
            'email' => 'admin@moderna.com',
            'password' => 'password',
        ]);

        $admin->assignRole('مدير');
    }
}
