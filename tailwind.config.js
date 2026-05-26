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
            // ── Wired Editorial Design System ─────────────────────────────
            fontFamily: {
                // Font naratif — digunakan pada judul artikel & headline besar
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                // Font fungsional — digunakan pada navigasi, label, tombol, metadata
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Palet Monokrom Mutlak
                primary:  '#000000',  // Teks utama & tombol utama
                canvas:   '#ffffff',  // Latar belakang (putih kertas)
                hairline: '#e0e0e0',  // Garis pemisah 1px
                muted:    '#6b6b6b',  // Teks metadata / keterangan
            },
        },
    },

    plugins: [forms],
};
