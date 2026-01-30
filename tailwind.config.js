import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff8f1',
                    100: '#feecdc',
                    200: '#fcd5c2', // Soft Orange Light
                    400: '#fdba74', // Soft Orange Main
                    500: '#fb923c', // Soft Orange Dark
                    900: '#1c1917', // Soft Black
                }
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
    darkMode: 'class',
};
