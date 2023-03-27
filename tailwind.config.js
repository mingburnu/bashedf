const colors = require('tailwindcss/colors')

module.exports = {
    mode: 'jit',
    purge: [
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {},
        colors: {
            // Build your palette here
            red: colors.red,
            green: colors.green,
            indigo: colors.indigo,
            purple: colors.purple,
            trueGray: colors.trueGray,
            black: {
                DEFAULT: colors.black
            },
            white: {
                DEFAULT: colors.white
            }
        },
    },
    variants: {
        extend: {},
    },
    plugins: [],
}
