import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Roboto', ...defaultTheme.fontFamily.sans],
                serif: ['Cormorant Garamond', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                blue: {
                    DEFAULT: 'var(--blue)',
                    hover: 'var(--blue-hover)',
                },
                stone: {
                    DEFAULT: 'var(--stone)',
                    hover: 'var(--stone-hover)',
                    15: 'var(--stone-15)',
                    30: 'var(--stone-30)',
                    '30-hover': 'var(--stone-30-hover)',
                    50: 'var(--stone-50)',
                },
                cyan: {
                    DEFAULT: 'var(--cyan)',
                    hover: 'var(--cyan-hover)',
                },
                orange: {
                    DEFAULT: 'var(--orange)',
                    hover: 'var(--orange-hover)',
                },
            },
            spacing: {
                112: '28rem',
            },
            boxShadow: {
                card: '0 1px 2px 0 rgba(0, 0, 0, 0.04), 0 1px 3px 0 rgba(0, 0, 0, 0.06)',
                elevated: '0 4px 12px -2px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04)',
            },
            borderRadius: {
                xl: '0.875rem',
                '2xl': '1.125rem',
            },
            maxWidth: {
                '8xl': '90rem',
            },
        },
        container: {
            center: true,
        },
    },

    corePlugins: {
        float: false,
    },

    plugins: [forms],
}
