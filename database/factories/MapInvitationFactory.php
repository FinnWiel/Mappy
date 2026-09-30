<?php

namespace Database\Factories;

use App\Models\Map;
use App\Models\MapInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MapInvitation>
 */
class MapInvitationFactory extends Factory
{
    protected $model = MapInvitation::class;

    public function definition(): array
    {
        return [
            'map_id' => Map::factory(),
            'invited_by' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'role' => Map::VIEWER,
            'token' => Str::random(48),
            'accepted_at' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (): array => ['accepted_at' => now()]);
    }

    public function editor(): static
    {
        return $this->state(fn (): array => ['role' => Map::EDITOR]);
    }
}
