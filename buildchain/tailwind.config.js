// module exports
module.exports = {
    content: [
        '../src/templates/**/*.{twig,html}',
        './src/vue/**/*.{vue,html}',
    ],
    safelist: [
        './src/css/components/*.css',
    ],
    theme: {
        extend: {
            gridTemplateColumns: {
                13: 'repeat(13, minmax(0, 1fr))',
            },
            minHeight: {
                16: '4rem',
                52: '13rem'
            },
            screens: {
                'px': '1px'
            },
            width: {
                128: '32rem',
                '1/7': '14.285%',
                '3/7': '42.855%',
            }
        }
    },
    important: true,
};
