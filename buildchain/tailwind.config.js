// module exports
module.exports = {
    mode: 'jit',
    purge: {
        content: [
            '../src/templates/**/*.{twig,html}',
            './src/vue/**/*.{vue,html}',
        ],
        layers: [
            'base',
            'components',
            'utilities',
        ],
        mode: 'layers',
        options: {
            whitelist: [
                './src/css/components/*.css',
            ],
        }
    },
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
    corePlugins: {},
    plugins: [],
    variants: {
        extend: {
            backgroundColor: ['peer'],
            color: ['group'],
            outline: ['peer'],
            opacity: ['peer'],
            pointerEvents: ['peer'],
            ring: ['peer'],
            ringColor: ['peer'],
            ringOpacity: ['peer']
        }
    }
};
