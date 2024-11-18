<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Designation>
 */
class DesignationFactory extends Factory
{
    private static $designationCounter = 0;
    private static $departmentCounter = 0;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $designations = [
            'Web developer',
            'HR Manager',
            'Customer Support Service',
        ];

        $departments = [
            1 => 'Information Technology',
            2 => 'Human Resource',
            3 => 'Operation',
        ];

        $designationName = $designations[self::$designationCounter % 3];
        self::$designationCounter++;

        $departmentId = array_keys($departments)[self::$departmentCounter % 3];
        self::$departmentCounter++;

        return [
            'name' => $designationName,
            'department_id' => $departmentId,
        ];
    }
}
