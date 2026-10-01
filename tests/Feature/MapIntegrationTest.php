<?php

namespace Tests\Feature;

use App\Models\Map;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_or_save_an_atlas(): void
    {
        $map = Map::factory()->create();

        $this->get(route('maps.show', $map))->assertRedirect(route('login'));
        $this->putJson(route('maps.save', $map), ['atlas' => []])->assertUnauthorized();
        $this->assertNull($map->fresh()->atlas);
    }

    public function test_uninvited_users_cannot_read_save_or_share_another_map(): void
    {
        $map = Map::factory()->create();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)->get(route('maps.show', $map))->assertForbidden();
        $this->putJson(route('maps.save', $map), ['atlas' => []])->assertForbidden();
        $this->post(route('maps.invite', $map), ['email' => 'guest@example.test', 'role' => Map::EDITOR])->assertForbidden();
        $this->assertNull($map->fresh()->atlas);
        $this->assertSame(0, $map->invitations()->count());
    }

    public function test_editor_saves_and_reloads_session_and_world_data_without_sharing_rights(): void
    {
        $map = Map::factory()->create();
        $editor = User::factory()->create();
        $map->assignRoleTo($editor, Map::EDITOR);
        $atlas = [
            'schemaVersion' => 2,
            'rootBoardId' => 'world',
            'boards' => ['world' => ['id' => 'world', 'name' => 'Integrated world', 'kind' => 'world', 'placeIds' => []]],
            'places' => [],
            'routes' => [],
            'sessions' => ['session' => ['id' => 'session', 'title' => 'First session', 'transcript' => "  We discovered a village.\n", 'notes' => '', 'discoveries' => '', 'proposals' => []]],
            'worlds' => ['world' => ['mode' => 'independent', 'settings' => ['backend' => 'azgaar'], 'biomes' => [], 'territories' => [], 'showPhysical' => false]],
        ];

        $this->actingAs($editor)->putJson(route('maps.save', $map), ['atlas' => $atlas])->assertOk();
        $this->assertSame($atlas, $map->fresh()->atlas);
        $this->assertSame('Integrated world', $map->fresh()->title);
        $this->get(route('maps.show', $map))->assertOk()
            ->assertSee('We discovered a village.')
            ->assertSee('id="sessions-button"', false)
            ->assertSee('id="layers-dialog"', false)
            ->assertSee('id="search-places"', false)
            ->assertDontSee('Share & access');
        $this->post(route('maps.invite', $map), ['email' => 'guest@example.test', 'role' => Map::EDITOR])->assertForbidden();
    }

    public function test_viewer_can_open_the_editor_but_cannot_save_or_share(): void
    {
        $map = Map::factory()->create();
        $viewer = User::factory()->create();
        $map->assignRoleTo($viewer, Map::VIEWER);

        $this->actingAs($viewer)->get(route('maps.show', $map))->assertOk()
            ->assertSee('map-read-only', false)
            ->assertSee('canEdit: false', false)
            ->assertDontSee('Share & access');
        $this->putJson(route('maps.save', $map), ['atlas' => []])->assertForbidden();
        $this->post(route('maps.invite', $map), ['email' => 'guest@example.test', 'role' => Map::EDITOR])->assertForbidden();
        $this->assertNull($map->fresh()->atlas);
    }

    public function test_invalid_atlas_does_not_replace_the_saved_map(): void
    {
        $owner = User::factory()->create();
        $map = Map::factory()->ownedBy($owner)->create();
        $map->assignRoleTo($owner, Map::OWNER);
        $title = $map->title;

        $this->actingAs($owner)->putJson(route('maps.save', $map), ['atlas' => ['rootBoardId' => 'missing', 'boards' => []]])->assertUnprocessable();
        $this->assertSame($title, $map->fresh()->title);
        $this->assertNull($map->fresh()->atlas);
    }
}
