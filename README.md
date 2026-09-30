# MapMaker

MapMaker is a Laravel 13 + Livewire atlas editor built around the original MapMaker3000 cartography interface.

## Included

- Livewire registration, sign-in, sign-out, and map library.
- Server-persisted atlas JSON with debounced saves from the editor.
- Map-scoped Spatie Permission teams: `map-owner`, `map-editor`, and `map-viewer`.
- Owner-only sharing panel that creates email-bound invitation links with editor or viewer rights.
- Viewer-only mode in the editor, with authorization enforced again on every save and sharing endpoint.

## Start locally

```bash
php artisan migrate
php artisan serve
```

Visit the displayed local URL, register an account, and create a map. The SQLite database is configured in `.env` by default.

## Access model

Each map is its own Spatie Permission team. Owners can edit and invite collaborators; editors can save map changes; viewers can open a read-only map. Invitations can only be accepted by the account matching the invited email address.

## Verification

```bash
php artisan test
```
