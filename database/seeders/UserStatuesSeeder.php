<?php

namespace Database\Seeders;

use App\Models\UserStatues;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserStatuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserStatues::create([
            "name"=> "Active"
        ]) ;
        UserStatues::create([
            "name"=> "Inactive"
        ]) ;
    }
}
