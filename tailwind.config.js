import forms from '@tailwindcss/forms';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f4f1ff',
                    100: '#ebe5ff',
                    200: '#d9cfff',
                    300: '#bea6ff',
                    400: '#a175ff',
                    500: '#8442ff',
                    600: '#7220fe', // Hostinger-like Purple
                    700: '#620ee4',
                    800: '#520bc0',
                    900: '#450a9b',
                }
            }
        },
    },

    plugins: [forms],
};
