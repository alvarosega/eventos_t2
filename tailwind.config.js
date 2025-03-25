/** @type {import('tailwindcss').Config} */
export default {
  // Escaneo de todos tus Blade y archivos front
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],

  // Activar modo oscuro por clase
  darkMode: 'class',

  theme: {
    fontFamily: {
      sans: ['Poppins', 'sans-serif'],
    },
    extend: {
      colors: {
        // Tema claro
        lightBg: '#F9FAFB',
        lightText: '#1F2937',
        lightCard: '#FFFFFF',

        // Tema oscuro
        darkBg: '#0F172A',
        darkText: '#F1F5F9',
        darkCard: '#1E293B',

        // Tonos de marca (puedes cambiarlos)
        primary: '#2F3645',    // Indigo 600
        secondary: '#64748B',  // Slate 500
        success: '#16a34a',
        danger: '#dc2626',
        warning: '#f59e0b',
        info: '#3b82f6',
      },
    },
  },
  plugins: [],
};
