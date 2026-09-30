<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class Map extends Model
{
    use HasFactory;

    public const OWNER = 'map-owner';

    public const EDITOR = 'map-editor';

    public const VIEWER = 'map-viewer';

    protected $fillable = ['owner_id', 'title', 'atlas'];

    protected function casts(): array
    {
        return ['atlas' => 'array'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(MapInvitation::class);
    }

    public function roleFor(User $user): ?string
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($this->id);

        return $user->roles()
            ->where('roles.map_id', $this->id)
            ->value('name');
    }

    public function canEdit(User $user): bool
    {
        return in_array($this->roleFor($user), [self::OWNER, self::EDITOR], true);
    }

    public function canManage(User $user): bool
    {
        return $this->roleFor($user) === self::OWNER;
    }

    public function assignRoleTo(User $user, string $role): void
    {
        abort_unless(in_array($role, [self::OWNER, self::EDITOR, self::VIEWER], true), 422);

        app(PermissionRegistrar::class)->setPermissionsTeamId($this->id);
        DB::table(config('permission.table_names.model_has_roles'))
            ->where(config('permission.column_names.team_foreign_key'), $this->id)
            ->where(config('permission.column_names.model_morph_key'), $user->getKey())
            ->where('model_type', $user::class)
            ->delete();

        $spatieRole = Role::query()->firstOrCreate([
            'name' => $role,
            'guard_name' => 'web',
            'map_id' => $this->id,
        ]);

        $user->assignRole($spatieRole);
    }
}
