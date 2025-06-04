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
        return [
            'phone_number' => $this->faker->unique()->numerify('03########'),
            'fullname' =>$this-> faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'avatar' => 'default.png',
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'birthday' => $this->faker->date(),
             'role' => $this->faker->randomElement(['admin', 'employee', 'customer']),
            'status' => 'active',
            'bank_name' => null,
            'user_bank_name' => null,
            'bank_account' => null,
            'reason_lock' => null,
            'is_change_password' => 1,
        
            'password' => static::$password ??= Hash::make('password'),
            
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
