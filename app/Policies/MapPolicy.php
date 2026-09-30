<?php

namespace App\Policies;

use App\Models\Map;
use App\Models\User;

class MapPolicy
{
    public function view(User $user, Map $map): bool
    {
        return $map->roleFor($user) !== null;
    }

    public function update(User $user, Map $map): bool
    {
        return $map->canEdit($user);
    }

    public function manage(User $user, Map $map): bool
    {
        return $map->canManage($user);
    }
}
