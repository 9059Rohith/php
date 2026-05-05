document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('[data-ajax-form]');

  forms.forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const response = await fetch(form.action, {
        method: form.method || 'POST',
        body: new FormData(form),
      });

      const data = await response.json();
      const target = document.querySelector('[data-toast-target]');
      if (target) {
        target.textContent = data.message || (data.success ? 'Saved' : 'Something went wrong');
        target.classList.toggle('success', !!data.success);
        target.classList.toggle('error', !data.success);
      }
    });
  });

  const photoInput = document.querySelector('[data-photo-input]');
  const preview = document.querySelector('[data-photo-preview]');
  if (photoInput && preview) {
    photoInput.addEventListener('change', () => {
      const file = photoInput.files && photoInput.files[0];
      if (!file) return;
      preview.src = URL.createObjectURL(file);
      preview.hidden = false;
    });
  }
});
