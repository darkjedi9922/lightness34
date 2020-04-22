const path = require('path');
const glob = require('glob');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const webpack = require('webpack');

function globs(entries) {
    let result = [];
    for (let i = 0; i < entries.length; i++) {
        const pattern = entries[i];
        result = result.concat(glob.sync(pattern));
    }
    return result;
}

module.exports = {
    context: __dirname,
    entry: {
        'admin.css': globs([
            './styles/admin.css',
            './styles/admin.scss'
        ]),
        'site.css': globs([
            './styles/site.css',
            './styles/site.scss'
        ]),
        'admin.js': './scripts/admin/main.ts'
    },
    output: {
        path: path.resolve(__dirname, './build'),
        filename: '[name]'
    },
    module: {
        rules: [
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    use: [
                        'css-loader',
                        'resolve-url-loader'
                    ]
                })
            },
            {
                test: /\.scss$/,
                include: path.resolve(__dirname, 'styles'),
                use: ExtractTextPlugin.extract({
                    use: [
                        'css-loader',
                        'resolve-url-loader',
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: true, // required by resolve-url-loader
                                sourceMapContents: false
                            }
                        }
                    ]
                })
            },
            {
                // Webpack не понимает @font-face в css без этого.
                test: /\.(png|jpg|jpeg|woff(2)?|eot|ttf|svg)$/,
                use: ['url-loader']
            },
            {
                test: /\.(ts|tsx)$/,
                use: ['babel-loader', 'ts-loader']
            }
        ]
    },
    plugins: [
        new ExtractTextPlugin('[name]'),
    ],
    resolve: {
        extensions: ['*', '.ts', '.tsx', '.js', '.jsx']
    }
}