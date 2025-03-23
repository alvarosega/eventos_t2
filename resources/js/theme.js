// resources/js/theme.js

document.addEventListener('DOMContentLoaded', () => {
    const htmlEl = document.documentElement;
    const themeToggleBtn = document.getElementById('themeToggleBtn');
  
    // Leer preferencia
    const savedTheme = localStorage.getItem('color-theme');
    if (savedTheme === 'dark') {
      htmlEl.classList.add('dark');
    } else {
      htmlEl.classList.remove('dark');
    }
  
    // Al hacer clic en el botón
    themeToggleBtn?.addEventListener('click', () => {
      htmlEl.classList.toggle('dark');
      // Guardar preferencia
      if (htmlEl.classList.contains('dark')) {
        localStorage.setItem('color-theme', 'dark');
      } else {
        localStorage.setItem('color-theme', 'light');
      }
    });
  });
  