<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName,
            "gender_id" => rand(1,2),
            'email' => $this->faker->email,
            'phone_number' => $this->faker->unique()->phoneNumber,
            'target' => $this->faker->numberBetween(5000, 20000),
            'status_id' => $this->faker->numberBetween(1, 2),
            'team_id' => $this->faker->numberBetween(1, 2),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function role1()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => 1,
            ];
        });
    }

    public function role2()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => 2,
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */

}
