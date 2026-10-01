// Keep requests ordered so an older edit cannot overwrite a newer one.
export function createServerSaver({canEdit, saveUrl, csrfToken, onStatus, onError, request = fetch}) {
  let pending = null, saving = false;
  async function flush() {
    saving = true;
    while (pending !== null) {
      const snapshot = pending;
      pending = null;
      onStatus('Saving…');
      try {
        const response = await request(saveUrl, {
          method: 'PUT',
          credentials: 'same-origin',
          headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken},
          body: JSON.stringify({atlas: JSON.parse(snapshot)}),
        });
        if (!response.ok) throw new Error(`Save failed (${response.status})`);
        if (pending === null) onStatus('Saved to your atlas');
      } catch (error) {
        onStatus('Save failed — export a backup or retry');
        onError(error);
      }
    }
    saving = false;
  }
  return {
    save(snapshot) {
      if (canEdit !== true) return;
      pending = snapshot;
      if (!saving) void flush();
    },
  };
}
