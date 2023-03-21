const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                serif: ['serif'],
                monospace: ['monospace'],
                // monospace: ['JetBrains Mono, monospace'],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
