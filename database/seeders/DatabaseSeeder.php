<?php

namespace Database\Seeders;

use App\Models\Map;
use App\Models\MapInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin Cartographer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $editor = User::query()->updateOrCreate(['email' => 'editor@example.com'], [
            'name' => 'Elara the Editor',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $viewer = User::query()->updateOrCreate(['email' => 'viewer@example.com'], [
            'name' => 'Vance the Viewer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $map = Map::query()->updateOrCreate([
            'owner_id' => $admin->id,
            'title' => 'The Shattered Realm',
        ]);
        $map->assignRoleTo($admin, Map::OWNER);
        $map->assignRoleTo($editor, Map::EDITOR);
        $map->assignRoleTo($viewer, Map::VIEWER);

        MapInvitation::query()->updateOrCreate([
            'map_id' => $map->id,
            'email' => $viewer->email,
        ], [
            'invited_by' => $admin->id,
            'role' => Map::VIEWER,
            'token' => str()->random(48),
            'accepted_at' => now(),
        ]);

        $secondMap = Map::query()->firstOrCreate([
            'owner_id' => $admin->id,
            'title' => 'Whispers of the Ash Coast',
        ]);
        $secondMap->assignRoleTo($admin, Map::OWNER);

        User::factory()->count(3)->create();
    }
}
