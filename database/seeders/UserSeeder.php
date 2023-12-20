<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Mohamed",
            "gender_id" => 1,
            "email" => "mohamed@admin.com",
            "phone_number" => "01121118319",
            "password" => Hash::make("mohamed11"),
            "role_id" => 1,
            "status_id" => 1
        ]);
        User::create([
            "name" => "Mohamed",
            "gender_id" => 1,
            "email" => "mohamed@sales.com",
            "phone_number" => "01221118319",
            "password" => Hash::make("mohamed11"),
            "target" => 4000,
            'team_id' => 1,
            "role_id" => 2,
            "status_id" => 1
        ]);

        User::create([
            "name" => "Mohamed",
            "gender_id" => 1,
            "email" => "mohamed@teamlead.com",
            "phone_number" => "01321118319",
            "password" => Hash::make("mohamed11"),
            "target" => 4000,
            'team_id' => 1,
            "role_id" => 3,
            "status_id" => 1
        ]);
        User::factory()->count(5)->role2()->create();

    }
}
