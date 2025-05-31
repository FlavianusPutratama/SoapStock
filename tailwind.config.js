// tailwind.config.js
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary-dark': '#203558',
                'primary': '#00A2E9',
                'primary-light': '#E6F6FE', // Untuk hover/active yang lebih lembut jika diperlukan
                'accent': '#FFD200',
                'accent-hover': '#FFE04D',
                'text-main': '#203558', // Warna teks utama
                'text-muted': '#5A6A82', // Warna teks sekunder/abu-abu
                'border-light': '#E0E7EF', // Warna border yang lebih halus
                'surface': '#FFFFFF',     // Latar belakang utama (kartu, bagian)
                'surface-alt': '#F8F9FA', // Latar belakang halaman global
                // Warna status (bisa disesuaikan agar lebih soft dari default Tailwind)
                'success': '#28a745', // atau Tailwind 'green-500'
                'danger': '#dc3545',  // atau Tailwind 'red-600'
                'warning': '#ffc107', // atau Tailwind 'yellow-500'
                'info': '#17a2b8',    // atau Tailwind 'cyan-500'
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};