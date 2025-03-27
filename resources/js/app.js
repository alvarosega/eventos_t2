import './bootstrap';
import { createIcons, icons } from 'lucide';

// Asegúrate de inicializar los íconos después de que la página cargue
document.addEventListener('DOMContentLoaded', function () {
    createIcons({ icons });
});
