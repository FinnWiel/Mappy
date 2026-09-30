<?php

namespace App\Http\Controllers;

use App\Models\Map;
use App\Models\MapInvitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MapController extends Controller
{
    public function save(Request $request, Map $map): JsonResponse
    {
        Gate::authorize('update', $map);
        $data = $request->validate(['atlas' => ['required', 'array']]);
        $atlas = $data['atlas'];

        abort_unless(isset($atlas['rootBoardId'], $atlas['boards'][$atlas['rootBoardId']]), 422, 'Invalid atlas root.');
        $title = $atlas['boards'][$atlas['rootBoardId']]['name'] ?? $map->title;
        abort_unless(is_string($title) && mb_strlen($title) <= 100, 422, 'Invalid map title.');

        $map->update(['atlas' => $atlas, 'title' => $title]);

        return response()->json(['saved_at' => $map->fresh()->updated_at?->toIso8601String()]);
    }

    public function invite(Request $request, Map $map): RedirectResponse
    {
        Gate::authorize('manage', $map);
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:'.Map::EDITOR.','.Map::VIEWER],
        ]);

        $invitation = $map->invitations()->updateOrCreate(
            ['email' => mb_strtolower($data['email'])],
            ['role' => $data['role'], 'invited_by' => $request->user()->id, 'accepted_at' => null]
        );

        return back()->with('invite_link', route('invitations.accept', $invitation->token));
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = MapInvitation::query()->where('token', $token)->firstOrFail();

        if (mb_strtolower($request->user()->email) !== mb_strtolower($invitation->email)) {
            abort(403, 'This invitation was sent to a different email address.');
        }

        $invitation->map->assignRoleTo($request->user(), $invitation->role);
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('maps.show', $invitation->map)->with('status', 'You now have access to this map.');
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
