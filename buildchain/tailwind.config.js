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
            minHeight: {
                16: '4rem',
                52: '13rem'
            },
            screens: {
                'px': '1px'
            }
        }
    },
    corePlugins: {},
    plugins: [],
    variants: {
        extend: {
            backgroundColor: ['peer'],
            display: ['peer'],
            outline: ['peer'],
            ring: ['peer'],
            ringColor: ['peer'],
            ringOpacity: ['peer']
        }
    }
};
