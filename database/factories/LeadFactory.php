<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "full_name" => $this->faker->name,
            "gender_id" => rand(1,2),
//            "email" => fake()->unique()->email,
            "phone_number" => $this->faker->unique()->phoneNumber,
//            "value" => rand(600,1000),
//            "company_name" => fake()->company,
//            "job_title" => fake()->jobTitle,
//            "address" => fake()->address,
//            "comment" => fake()-> paragraph,
//            "sales_id" => rand(1,7),
            "status_id" => 1
        ];
    }
}
