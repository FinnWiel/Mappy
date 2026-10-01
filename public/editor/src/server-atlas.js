// PHP's associative-array JSON cast returns empty dictionaries as [].
// Restore only schema dictionary fields; list fields must stay arrays.
export function normalizeServerAtlas(input) {
  const atlas = structuredClone(input);
  function restore(owner, keys) {
    if (!owner || typeof owner !== 'object') return;
    for (const key of keys) {
      if (Array.isArray(owner[key]) && owner[key].length === 0) owner[key] = {};
    }
  }
  restore(atlas, ['boards', 'places', 'routes', 'sessions', 'worlds']);
  for (const world of Object.values(atlas?.worlds || {})) restore(world, ['geography', 'biomes', 'territories']);
  // Older saves passed through Laravel's empty-string-to-null middleware.
  function restoreText(owner, keys) {
    for (const key of keys) if (owner?.[key] === null) owner[key] = '';
  }
  for (const place of Object.values(atlas?.places || {})) {
    restoreText(place, ['description', 'notes']);
    for (const event of place.events || []) restoreText(event, ['detail', 'session']);
  }
  for (const route of Object.values(atlas?.routes || {})) restoreText(route, ['description', 'notes']);
  for (const session of Object.values(atlas?.sessions || {})) {
    restoreText(session, ['notes', 'transcript', 'discoveries']);
    restoreText(session.discoveryReview, ['source']);
    for (const proposal of session.discoveryReview?.proposals || []) restoreText(proposal, ['type', 'name', 'detail', 'source', 'from', 'to', 'target']);
  }
  return atlas;
}
