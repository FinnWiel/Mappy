import test from 'node:test';
import assert from 'node:assert/strict';
import {normalizeServerAtlas} from './server-atlas.js';
import {blankAtlas} from './blank-atlas.js';
import {saveSessionRecord} from './session-records.js';
import {validateAtlas, previewAtlasUpgrade} from './atlas.js';

function phpRoundTrip(value) {
  if (Array.isArray(value)) return value.map(phpRoundTrip);
  if (value !== null && typeof value === 'object') {
    return Object.keys(value).length ? Object.fromEntries(Object.entries(value).map(([key, child]) => [key, phpRoundTrip(child)])) : [];
  }
  return value;
}

test('empty version 1 and version 2 atlases remain editable after a Laravel JSON round-trip', () => {
  const blank = blankAtlas('world');
  for (const atlas of [blank, previewAtlasUpgrade(blank).atlas]) {
    const saved = phpRoundTrip(atlas);
    const normalized = normalizeServerAtlas(saved);
    const loaded = validateAtlas(normalized);
    assert.deepEqual(loaded.places, {});
    assert.deepEqual(loaded.routes, {});
    assert.deepEqual(loaded.sessions, {});
    assert.deepEqual(loaded.boards.world.placeIds, []);
    if (atlas.schemaVersion === 2) assert.deepEqual(loaded.worlds.world.biomes, {});
    assert.deepEqual(saved.places, []);
  }
});

test('server normalization preserves session lists and rejects malformed populated dictionaries', () => {
  const atlas = blankAtlas('world');
  atlas.sessions = {session:{id:'session', name:'Earlier session', proposals:[]}};
  const loaded = validateAtlas(normalizeServerAtlas(phpRoundTrip(atlas)));
  assert.deepEqual(loaded.sessions.session.proposals, []);
  assert.throws(() => validateAtlas(normalizeServerAtlas({...atlas, places:[{id:'bad'}]})), /Invalid atlas/);
});

test('legacy server nulls in optional session text load without changing source text', () => {
  const atlas = saveSessionRecord(blankAtlas('world'), {id:'session', title:'First session', date:'2026-10-01',
    notes:'', transcript:'  Source text\n', discoveries:'', startPlaceId:null, visitedPlaceIds:[], discoveryPlaceIds:[]});
  const saved = phpRoundTrip(atlas);
  saved.sessions.session.notes = null;
  saved.sessions.session.discoveries = null;
  const loaded = validateAtlas(normalizeServerAtlas(saved));
  assert.equal(loaded.sessions.session.notes, '');
  assert.equal(loaded.sessions.session.discoveries, '');
  assert.equal(loaded.sessions.session.transcript, '  Source text\n');
});
