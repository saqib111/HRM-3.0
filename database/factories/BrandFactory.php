<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    // Static counter to alternate between brand names
    private static $brandCounter = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define the brands in the desired order
        $brands = [
            'Baji PKR',  // First brand
            'Baji BDT',  // Second brand
            'E2 BDT',    // Third brand
        ];

        // Assign brand name based on the counter value
        $brandName = $brands[self::$brandCounter % 3]; // Alternates between 0, 1, and 2

        // Increment the counter after each call to the factory
        self::$brandCounter++;

        return [
            'name' => $brandName,  // Store the brand name
        ];
    }
}
