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
                // 1. BRAND UTAMA (Soft Orange - Pilihan Anda)
                brand: {
                    50: '#fff8f1',
                    100: '#feecdc',
                    200: '#fcd5c2',
                    400: '#fdba74',
                    500: '#fb923c',
                    600: '#f97316', // Tambahan utk tombol hover
                    700: '#ea580c', // Tambahan utk teks gelap
                    900: '#1c1917', // Soft Black (Teks Utama)
                },
                // 2. GOODS (Barang) -> Kita samakan dengan Brand agar "Simpel"
                goods: {
                    50: '#fff8f1',
                    100: '#feecdc',
                    600: '#fb923c', // Soft Orange Dark
                    700: '#ea580c',
                },
                // 3. SERVICES (Jasa) -> Biru Soft (Agar tetap kontras tapi kalem)
                services: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    600: '#60a5fa', // Soft Blue
                    700: '#3b82f6',
                }
            },
            fontFamily: {
                // Font Inter (Lebih bersih/modern)
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
    darkMode: 'class',
};
