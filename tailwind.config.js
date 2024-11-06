/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        background: {
          main: '#fffffe',
          secondary: '#eaddcf',
          section: '#FAFAF9', // Custom bg-section color
          muted: '#f3f4f6',
        },
        highlight: '#8c7851',
        text: {
          standard: '#020826',
          higlighed: '#716040',
          muted: '#6b7280',
        },
        border: {
          default: '#D1D5DB',
        },
        button: {
          new: '#4caf50',
          update: '#2196f3',
          delete: '#f44336',
          hover: {
            new: '#388e3c',
            update: '#1976d2',
            delete: '#d32f2f',
          },
        },
      },
      fontFamily: {
        sans: ['Roboto', 'sans-serif'],
      },
      fontSize: {
        lg: '1.125rem',
        base: '1rem',
        xl: '1.25rem',
        sm: '0.875rem',
      },
    },
  },
  plugins: [],
}
