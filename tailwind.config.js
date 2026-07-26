import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        '-translate-x-full',
        'translate-x-0',
        'hidden',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'hijau-tua': '#14532D',
                'hijau-muda': '#22C55E',
                'abu-muda': '#F1F5F9',
                'merah': '#DC2626',
                'kuning': '#FACC15',
                'biru': '#2563EB',
            }
        },
    },

    plugins: [forms],
};
