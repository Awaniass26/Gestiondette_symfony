/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
    colors: {
      'custom-blue-dark': '#2D3E55', 
      'custom-blue-ld': '#2B5583', 
      'custom-blue-light': '#2B6BAE', 
      'blanc':'#FFFF'
    },
 
  },
  plugins: [],
}