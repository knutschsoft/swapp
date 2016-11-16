var isProduction = process.env.NODE_ENV === 'production',
    BowerWebpackPlugin = require('bower-webpack-plugin'),
    path = require('path');

module.exports = {
    entry: {
        bootstraploader: 'bootstrap-loader',
        global: './app/Resources/assets/js/pages/global.js'
    },
    output: {
        filename: '[name].bundle.js',
        path: 'web/js',
        publicPath: isProduction ? '/js/' : 'http://localhost:8092/js/'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: "babel-loader"
            },
            {
                test: /\.css$/,
                loader: "style-loader!css"
            },
            {
                test:   /\.(png|gif|jpe?g|svg?(\?v=[0-9]\.[0-9]\.[0-9])?)$/i,
                loader: 'url-loader?limit=10000'
            },
            {
                test: /\.scss$/,
                loader: "style-loader!css-loader!sass-loader"
            },
            { test: /bootstrap-sass\/assets\/javascripts\//, loader: 'imports-loader?jQuery=jquery' },
            { test: /\.(woff2?|svg)$/, loader: 'url-loader?limit=10000' },
            { test: /\.(ttf|eot)$/, loader: 'file-loader' }
        ]
    },
    plugins: [
        new BowerWebpackPlugin()
    ],
    externals: [
        {
            'window': 'window'
        }
    ],
    devtool: isProduction ? '#source-map' : '#inline-source-map',
    devServer: {
        hot: false,
        contentBase: './web/',
        headers: {
            "Access-Control-Allow-Origin": "*"
        },
        port: 8092
    },
    resolveLoader: { //only used for bc on webpack beta version as long as not all loaders/plugins support "-loader"
        moduleExtensions: ['-loader']
    }
};
