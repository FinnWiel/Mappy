<?php

namespace Tests\Feature;

use App\Models\Map;
use App\Models\MapInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_are_scoped_to_a_single_map_and_guard_map_updates(): void
    {
        $owner = User::factory()->create();
        $editor = User::factory()->create();
        $viewer = User::factory()->create();
        $map = Map::create(['owner_id' => $owner->id, 'title' => 'The Glass Sea']);

        $map->assignRoleTo($owner, Map::OWNER);
        $map->assignRoleTo($editor, Map::EDITOR);
        $map->assignRoleTo($viewer, Map::VIEWER);
        $otherMap = Map::create(['owner_id' => $owner->id, 'title' => 'The Silent North']);
        $otherMap->assignRoleTo($viewer, Map::EDITOR);

        $this->assertSame(Map::OWNER, $map->roleFor($owner));
        $this->assertTrue($map->canEdit($editor));
        $this->assertFalse($map->canEdit($viewer));
        $this->assertSame(Map::EDITOR, $otherMap->roleFor($viewer));

        $atlas = ['rootBoardId' => 'world', 'boards' => ['world' => ['name' => 'The Glass Sea']]];

        $this->actingAs($viewer)->putJson(route('maps.save', $map), ['atlas' => $atlas])->assertForbidden();
        $this->actingAs($editor)->putJson(route('maps.save', $map), ['atlas' => $atlas])->assertOk();

        $this->assertSame('The Glass Sea', $map->fresh()->title);
        $this->assertSame($atlas, $map->fresh()->atlas);
    }

    public function test_an_invitation_only_works_for_the_invited_email(): void
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create(['email' => 'guest@example.test']);
        $otherUser = User::factory()->create(['email' => 'other@example.test']);
        $map = Map::create(['owner_id' => $owner->id, 'title' => 'Moonfall']);
        $map->assignRoleTo($owner, Map::OWNER);

        $this->actingAs($owner)->post(route('maps.invite', $map), [
            'email' => $guest->email,
            'role' => Map::VIEWER,
        ])->assertRedirect();

        $token = $map->invitations()->firstOrFail()->token;
        $this->actingAs($otherUser)->get(route('invitations.accept', $token))->assertForbidden();
        $this->actingAs($guest)->get(route('invitations.accept', $token))->assertRedirect(route('maps.show', $map));

        $this->assertSame(Map::VIEWER, $map->fresh()->roleFor($guest));
    }

    public function test_authenticated_owner_can_open_the_livewire_map_library_and_editor(): void
    {
        $owner = User::factory()->create();
        $map = Map::create(['owner_id' => $owner->id, 'title' => 'Ashen Coast']);
        $map->assignRoleTo($owner, Map::OWNER);

        $this->actingAs($owner)->get(route('maps.index'))
            ->assertOk()
            ->assertSee('Ashen Coast');

        $this->actingAs($owner)->get(route('maps.show', $map))
            ->assertOk()
            ->assertSee('Share & access', false)
            ->assertSee('window.mapmakerConfig', false);
    }

    public function test_map_and_invitation_factories_create_a_valid_collaboration_fixture(): void
    {
        $owner = User::factory()->create();
        $map = Map::factory()->ownedBy($owner)->create();
        $invitation = MapInvitation::factory()->editor()->for($map)->create([
            'invited_by' => $owner->id,
        ]);

        $this->assertTrue($map->is($invitation->map));
        $this->assertSame(Map::EDITOR, $invitation->role);
        $this->assertNotEmpty($invitation->token);
    }
}
