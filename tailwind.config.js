/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
  ],
  theme: {
    extend:  {
      colors: {
        'gold': {
          light: '#f9e26c',
          DEFAULT: '#b98f42',
          dark: '#d6bf5a',
        }
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}

