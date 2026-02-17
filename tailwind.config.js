const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Helvetica Neue', 'Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "main-green-bg": "#B5E941",
                "main-green-bg-hover": "#d3f28d",
                "main-white": "#ffffff",
                "main-gray": "#f2f2f2",
                "main-black-title": "#202020",
                "main-black-text": "#4D4D4D",
                "main-green-text": "#508200",
                "main-green-text-light": "#797979",
                "main-dark-green": "#1D4B00",
                "main-price-color": "#326916",
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
}
