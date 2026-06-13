/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                cyber: {
                    bg:      '#0d0d0d',
                    surface: '#141414',
                    card:    '#161616',
                    border:  '#1e1e1e',
                    muted:   '#2a2a2a',
                    text:    '#c8c8c8',
                    dim:     '#555555',
                    violet:  '#7f77dd',
                    green:   '#22c55e',
                    amber:   '#ef9f27',
                    red:     '#e24b4a',
                    teal:    '#5dcaa5',
                },
            },
            fontFamily: {
                mono: ['"Courier New"', 'monospace'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
