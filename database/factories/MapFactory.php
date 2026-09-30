<?php

namespace Database\Factories;

use App\Models\Map;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Map>
 */
class MapFactory extends Factory
{
    protected $model = Map::class;

    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'title' => fake()->unique()->words(3, true),
            'atlas' => null,
        ];
    }

    public function ownedBy(User $user): static
    {
        return $this->state(fn (): array => ['owner_id' => $user->id]);
    }

    public function withAtlas(array $atlas): static
    {
        return $this->state(fn (): array => ['atlas' => $atlas]);
    }
}
