/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
  ],
  theme: {
    extend:  {
      colors: {
        gold: '#ECC440',
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}

