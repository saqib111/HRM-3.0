<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnnualLeaves>
 */
class AnnualLeavesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define leave type days (14 or 28)
        $leave_type_days = $this->faker->randomElement([14, 28]);

        // Simulate the joining date for the user
        // We'll randomly pick a date between Jan 1st and Dec 31st of the current year.
        $joining_date = $this->faker->dateTimeBetween('2023-01-01', '2023-12-31');
        $joining_date = date_create($joining_date->format('Y-m-d')); // Convert to DateTime

        // Set the end of the year (Dec 31st of current year)
        $end_of_year = date_create(date("Y") . "-12-31");

        // Calculate the remaining days in the year
        $remaining_days = date_diff($joining_date, $end_of_year)->days;
        $total_days_in_year = 365; // Adjust if it's a leap year or use 365 days

        // Calculate the annual leave balance based on remaining days in the year
        $calculated_leaves = ($remaining_days / $total_days_in_year) * $leave_type_days;

        // Apply rounding logic as per your previous code
        $decimal_part = $calculated_leaves - floor($calculated_leaves);
        $decimal_part = round($decimal_part, 4);

        if ($decimal_part >= 0.76) {
            $calculated_leaves = ceil($calculated_leaves);
        } elseif ($decimal_part >= 0.26 && $decimal_part <= 0.75) {
            $calculated_leaves = floor($calculated_leaves) + 0.5;
        } else {
            $calculated_leaves = floor($calculated_leaves);
        }

        // Return the data for the factory

        return [
            'user_id' => \App\Models\User::factory(),
            'leave_type' => $leave_type_days, // Leave type (14 or 28)
            'leave_balance' => $calculated_leaves, // Calculated leave balance
        ];
    }
}
