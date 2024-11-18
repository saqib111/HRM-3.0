<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    private static $departmentCounter = 0;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $departments = [
            'Information Technology',
            'Human Resource',
            'Operation',
        ];


        $departmentName = $departments[self::$departmentCounter % 3]; // Alternates between 0, 1, and 2
        self::$departmentCounter++;

        return [
            'name' => $departmentName,
        ];
    }
}
