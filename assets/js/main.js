document.addEventListener('DOMContentLoaded', () => {
  const jobFilterForm = document.querySelector('#jobFilterForm');
  if (jobFilterForm) {
    jobFilterForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      const params = new URLSearchParams(new FormData(jobFilterForm));
      const res = await fetch(`/ajax/jobs.php?${params.toString()}`);
      const html = await res.text();
      document.querySelector('#jobsContainer').innerHTML = html;
    });
  }

  document.querySelectorAll('form[data-validate]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });
});
