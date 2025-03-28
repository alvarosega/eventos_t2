/** @type {import('tailwindcss').Config} */
export default {
  // Escanea todas las plantillas Blade, JS y Vue
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],

  // Activa el modo oscuro por clase
  darkMode: 'class',

  theme: {
    fontFamily: {
      sans: ['Poppins', 'sans-serif'],
    },
    extend: {
      colors: {
        // Modo claro
        lightBg: '#E0E7FF',        // fondo con tono frío
        lightText: '#0F172A',
        lightCard: '#FFFFFF',
    
        // Modo oscuro base futurista
        darkBg: '#070617',         // negro profundo con matiz púrpura
        darkText: '#E0E7FF',       // blanco azulado
        darkCard: '#1A103D',       // púrpura oscuro semi-brillante
    
        // Acentos neón ciberpunk
        primary: '#092429',        // fucsia neón
        secondary: '#322566',      // cian neón
        accentBlue: '#00b7ff',     // azul neón (complemento útil)
        accentPink: '#ff4fff',     // rosa neón intenso
    
        success: '#00ff9c',        // verde neón
        danger: '#ff3c6d',
        warning: '#ffe600',
        info: '#5ef1ff',
    
        glow: '#ff00ff',
      },
    
      boxShadow: {
        neon: '0 0 10px #ff00ff, 0 0 20px #00ffff, 0 0 30px #ff00ff',
        softGlow: '0 0 8px rgba(255, 0, 255, 0.5), 0 0 4px rgba(0, 255, 255, 0.4)',
        cardGlow: '0 4px 20px rgba(255, 0, 255, 0.2)',
      },
    
      backdropBlur: {
        xs: '2px',
        md: '6px',
      }
    }
    ,
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('tailwindcss-animate'),
  ],
  
};
