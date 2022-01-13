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
            },
            width: {
                128: '32rem'
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
