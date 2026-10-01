import test from 'node:test';
import assert from 'node:assert/strict';
import {createServerSaver} from './server-save.js';

const tick = () => new Promise(resolve => setImmediate(resolve));

test('server saves use Laravel CSRF and preserve the latest edit in request order', async () => {
  const requests = [], statuses = [], finish = [];
  const saver = createServerSaver({canEdit:true, saveUrl:'/maps/1/atlas', csrfToken:'csrf',
    onStatus: status => statuses.push(status), onError: assert.fail,
    request: (url, options) => {
      requests.push({url, options});
      return new Promise(resolve => finish.push(resolve));
    },
  });
  saver.save(JSON.stringify({name:'first', sessions:{session:{transcript:'source'}}}));
  saver.save(JSON.stringify({name:'second'}));
  saver.save(JSON.stringify({name:'latest'}));
  assert.equal(requests.length, 1);
  assert.equal(requests[0].url, '/maps/1/atlas');
  assert.equal(requests[0].options.credentials, 'same-origin');
  assert.equal(requests[0].options.headers['X-CSRF-TOKEN'], 'csrf');
  assert.equal(requests[0].options.method, 'PUT');
  assert.equal(JSON.parse(requests[0].options.body).atlas.sessions.session.transcript, 'source');
  finish[0]({ok:true});
  await tick();
  assert.equal(requests.length, 2);
  assert.equal(JSON.parse(requests[1].options.body).atlas.name, 'latest');
  assert.equal(statuses.at(-1), 'Saving…');
  finish[1]({ok:true});
  await tick();
  assert.equal(statuses.at(-1), 'Saved to your atlas');
});

test('viewers never issue save requests', () => {
  for (const canEdit of [false, undefined]) {
    const saver = createServerSaver({canEdit, request:assert.fail, onStatus:assert.fail, onError:assert.fail});
    saver.save('{}');
  }
});

test('a refused save reports failure and a later edit can retry', async () => {
  const statuses = [], errors = [];
  let attempts = 0;
  const saver = createServerSaver({canEdit:true, saveUrl:'/maps/1/atlas', csrfToken:'csrf',
    onStatus:status => statuses.push(status), onError:error => errors.push(error),
    request:async () => ({ok:++attempts > 1, status:403}),
  });
  saver.save('{}');
  await tick();
  assert.match(statuses.at(-1), /Save failed/);
  assert.equal(errors.length, 1);
  saver.save('{}');
  await tick();
  assert.equal(statuses.at(-1), 'Saved to your atlas');
});
