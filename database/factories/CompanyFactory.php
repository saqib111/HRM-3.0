<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{

    private static $companyCounter = 0;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define the company names based on the static counter
        $companies = ['Aurora', 'E2'];

        // Alternate between 'Aurora' (index 0) and 'E2' (index 1)
        $companyName = $companies[self::$companyCounter % 2];  // Alternates between 0 and 1

        // Increment the counter after each call to the factory
        self::$companyCounter++;

        return [
            'name' => $companyName,  // Store the company name ('Aurora' or 'E2')
        ];
    }
}
