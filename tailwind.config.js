/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class',
  theme: {
    fontFamily: {
      sans: ['Poppins', 'sans-serif'],
    },
    extend: {
      colors: {
        // Modo claro
        lightBg: '#F0F4FF',
        lightText: '#0A1126',
        lightCard: '#FFFFFF',
        
        // Modo oscuro
        darkBg: '#0A0818',
        darkText: '#E6EDFF',
        darkCard: '#1E143F',
        
        // Acentos
        primary: '#0D2B32',
        primaryLight: '#00C6FF',
        secondary: '#3A1D7A',
        accentBlue: '#00D1FF',
        accentPink: '#FF2DF7',
        
        // Estados
        success: '#00F0A8',
        danger: '#FF2A6D',
        warning: '#FFE600',
        info: '#00F7FF',
        
        // Efectos
        glow: 'rgba(0, 255, 255, 0.7)',
        glowPink: 'rgba(255, 0, 255, 0.6)'
      },
      boxShadow: {
        neon: '0 0 8px var(--tw-shadow-color), 0 0 12px var(--tw-shadow-color)',
        softGlow: '0 0 6px rgba(0, 209, 255, 0.4), 0 0 3px rgba(255, 45, 247, 0.3)',
        cardGlow: '0 2px 12px rgba(0, 198, 255, 0.15)',
        innerGlow: 'inset 0 0 8px rgba(0, 213, 255, 0.2)',
        button: '0 0 8px rgba(0, 240, 168, 0.5), 0 2px 8px rgba(0, 0, 0, 0.15)'
      },
      backdropBlur: {
        xs: '3px',
        md: '8px',
        lg: '12px'
      },
      backgroundImage: {
        'primary-gradient': 'linear-gradient(135deg, #0D2B32 0%, #1E143F 100%)',
        'accent-gradient': 'linear-gradient(90deg, #00D1FF 0%, #FF2DF7 100%)',
        'success-gradient': 'linear-gradient(135deg, #00F0A8 0%, #00C6FF 100%)'
      },
      animation: {
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'glow': 'glow 2s ease-in-out infinite alternate'
      },
      keyframes: {
        glow: {
          'from': { boxShadow: '0 0 5px rgba(0, 213, 255, 0.5)' },
          'to': { boxShadow: '0 0 15px rgba(0, 213, 255, 0.8)' }
        }
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('tailwindcss-animate')
  ]
};