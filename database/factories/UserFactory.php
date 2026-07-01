<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
     * Includes all NOT NULL columns added in Prompt 3:
     *   username  — required, unique
     *   role_id   — defaults to VIEWER role (looked up or falls back to 1)
     *   is_active — defaults to 'Y'
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Look up the VIEWER role id; fall back to 1 if roles table not seeded yet
        $viewerRoleId = Role::where('role_name', 'VIEWER')->value('role_id') ?? 1;

        return [
            'name'              => fake()->name(),
            'username'          => fake()->unique()->userName(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'role_id'           => $viewerRoleId,
            'is_active'         => 'Y',
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

    /**
     * Set the user's role to ADMIN.
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            $adminRoleId = Role::where('role_name', 'ADMIN')->value('role_id') ?? 1;
            return ['role_id' => $adminRoleId];
        });
    }

    /**
     * Set the user as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => 'N',
        ]);
    }
}
