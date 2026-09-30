<main class="maps-page">
    <header class="maps-nav"><a class="maps-brand" href="{{ route('maps.index') }}"><span class="brand-sigil">✥</span><span><strong>MapMaker</strong> <em>3000</em><small>CAMPAIGN ATLAS</small></span></a><form method="POST" action="{{ route('logout') }}">@csrf <button class="text-button" type="submit">Sign out</button></form></header>
    <section class="maps-content">
        <div class="maps-intro"><div class="intro-rule"><span>✦</span></div><div><p class="eyebrow">YOUR CAMPAIGN ATLAS</p><h1>Worlds worth returning to.</h1><p>Create a map for your next campaign, then give each collaborator exactly the access they need.</p></div></div>
        <section class="new-map-card">
            <span class="corner corner-top-left"></span><span class="corner corner-top-right"></span><span class="corner corner-bottom-left"></span><span class="corner corner-bottom-right"></span>
            <div class="new-map-heading"><p class="eyebrow">NEW MAP</p><h2>Start a fresh atlas</h2><p>Name the next chapter in your campaign.</p></div>
            <form wire:submit="createMap" class="new-map-form"><input wire:model="title" maxlength="100" placeholder="The Shattered Realm" required><button class="primary-button" type="submit">Create map</button></form>
            @error('title') <p class="form-error">{{ $message }}</p> @enderror
        </section>
        <section class="map-list"><div class="section-title"><div><p class="eyebrow">THE LIBRARY</p><h2>Your maps</h2></div><span class="section-flourish">✦</span></div><div class="map-grid">
          @forelse ($maps as $map)
            @php($role = $map->roleFor(auth()->user()))
            <a wire:navigate href="{{ route('maps.show', $map) }}" class="map-card"><span class="map-card-frame"><span class="map-card-art">✦</span></span><div><p class="eyebrow">{{ $role === 'map-owner' ? 'OWNER' : ($role === 'map-editor' ? 'CAN EDIT' : 'VIEW ONLY') }}</p><h3>{{ $map->title }}</h3><p>Updated {{ $map->updated_at->diffForHumans() }}</p></div><span class="map-card-arrow">Open atlas <b>→</b></span></a>
          @empty
            <p class="empty-maps">Your first world is waiting to be charted.</p>
          @endforelse
        </div></section>
    </section>
</main>
