document.addEventListener('click', async (event) => {
  const action = event.target.closest('[data-join-waitlist]');
  if (!action) return;
  const response = await fetch('/event-registration/api/waitlist.php', { method: 'POST', body: new FormData(Object.assign(document.createElement('form'), { innerHTML: `<input name="action" value="join"><input name="event_id" value="${action.dataset.eventId}"><input name="_csrf" value="${document.querySelector('input[name=\"_csrf\"]')?.value || ''}">` })) });
  await response.json();
});
