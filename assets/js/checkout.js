document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.checkout-form');
  if (!form) return;

  const submitBtn = form.querySelector('button[type="submit"]');

  form.addEventListener('submit', (e) => {
    const required = form.querySelectorAll('[required]');
    let valid = true;
    let firstError = null;

    required.forEach(field => {
      const feedback = field.parentElement.querySelector('.invalid-feedback');
      if (!field.value.trim()) {
        valid = false;
        field.classList.add('is-invalid');
        if (feedback) feedback.textContent = 'This field is required.';
        if (!firstError) firstError = field;
      } else {
        field.classList.remove('is-invalid');
        if (feedback) feedback.textContent = '';
      }

      if (field.type === 'email' && field.value.trim()) {
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
          valid = false;
          field.classList.add('is-invalid');
          if (feedback) feedback.textContent = 'Please enter a valid email.';
          if (!firstError) firstError = field;
        }
      }
    });

    if (!valid) {
      e.preventDefault();
      if (firstError) firstError.focus();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing…';
  });
});
