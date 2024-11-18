<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\VisaInfo;
use App\Models\AssignedLeaveApprovals;
use App\Models\Emergency;
use App\Models\Dependant;
use App\Models\AnnualLeaves;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        // Define your departments and designations
        $departments = [
            1 => 'Information Technology', // ID 1
            2 => 'Human Resource Management', // ID 2
            3 => 'Operation', // ID 3
        ];

        // Designations mapped to department IDs
        $designations = [
            1 => 'Web Developer', // ID 1
            2 => 'HR', // ID 2
            3 => 'Customer Support Service', // ID 3
        ];

        // Define available brands
        $brands = [1, 2, 3]; // Brand IDs


        // Randomly select department ID
        $departmentId = fake()->randomElement(array_keys($departments));

        // Randomly determine which brands to include
        $possibleCombinations = [
            [1],
            [1, 2],
            [1, 2, 3],
            [2, 3],
            [1, 3],
            [2],
            [3],
        ];

        // Randomly select a combination of brand IDs
        $selectedBrandIds = fake()->randomElement($possibleCombinations);
        // Convert the array of brand IDs to a comma-separated string
        $brandString = implode(',', $selectedBrandIds);




        return [
            'username' => fake()->unique()->userName(),
            'employee_id' => $this->generateEmployeeId(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'joining_date' => now(), // Set to current date
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'confirmation_status' => '1',
            'image' => 'images/default_profile_picture.png',
            'company_id' => fake()->randomElement([1, 2]), // Randomly set to 1 or 2
            'department_id' => $departmentId, // Set the randomly selected department ID
            'designation_id' => $departmentId, // Set the designation ID to match the department ID
            'brand' => $brandString, // Store as a comma-separated string
            'week_days' => fake()->randomElement(['5', '6']), // Use a boolean value or adjust as needed
            'status' => '1', // Set to default status if applicable
            'role' => '1', // Set to default role if applicable
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    /**
     * Generate a fake employee ID.
     *
     * @return string
     */
    private function generateEmployeeId(): string
    {
        return 'EMP' . strtoupper(fake()->unique()->lexify('????')) . fake()->randomNumber(3, true);
    }
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    /**
     * Create related models along with the user.
     * 
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withRelatedModels(): static
    {
        return $this->afterCreating(function (User $user) {
            // Create related models for each user after the user is created
            UserProfile::factory()->create(['user_id' => $user->id]);
            VisaInfo::factory()->create(['user_id' => $user->id]);
            AssignedLeaveApprovals::factory()->create(['user_id' => $user->id]);
            Emergency::factory()->create(['user_id' => $user->id]);
            Dependant::factory()->create(['user_id' => $user->id]);
            AnnualLeaves::factory()->create(['user_id' => $user->id]);
        });
    }
}
