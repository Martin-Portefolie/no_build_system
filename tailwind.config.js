/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Roboto', 'sans-serif'],
      },
      fontSize: {
        'xs': '0.75rem',     // Extra small text, e.g., captions
        'sm': '0.875rem',     // Small text, e.g., subheadings
        'base': '1rem',       // Default body text size
        'lg': '1.125rem',     // Slightly larger body or subtitle text
        'xl': '1.25rem',      // Subheadings
        '2xl': '1.5rem',      // Main headings
        '3xl': '1.875rem',    // Larger main headings
        '4xl': '2.25rem',     // Large display text
        '5xl': '3rem',        // Extra-large display text
      },
      colors: {
        background: {
          main: '#fffffe',
          card: '#eaddcf',
        },
        text: {
          headline: '#020826',
          subheadline: '#716040',
          cardHeading: '#020826',
          cardParagraph: '#716040',
        },
        icon: {
          stroke: '#020826',
        },
        main: '#fffffe',
        neutral: '#8a817c',
        update: '#4a90e2',
        new: '#4caf50',
        delete: '#f44336',
        secondary: '#eaddcf',
        tertiary: '#f25042',
      },
    },
  },
  plugins: [],
};
