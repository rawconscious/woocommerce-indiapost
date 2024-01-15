/**
 * WordPress dependencies
 */
const defaultConfig = require('./node_modules/@wordpress/scripts/config/webpack.config');

/**
 * Local dependencies
 */
const path = require('path');

module.exports = [
    {
        ...defaultConfig,
        devtool: 'source-map',
        devServer: {
            ...defaultConfig.devServer,
            allowedHosts: 'all',   // That solved it
        },
        module: {
            ...defaultConfig.module,
            rules: [
                ...defaultConfig.module.rules,
                {
                    test: /\.(css)$/i,
                    use: ['style-loader', 'css-loader', 'postcss-loader'],
                },
            ],
        },
    },
];