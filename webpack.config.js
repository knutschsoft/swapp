var isProduction = process.env.NODE_ENV === 'production';
var path = require('path');
var dest = path.resolve(__dirname, 'web/js');
var bower = path.resolve(__dirname, 'bower_components');
var node = path.resolve(__dirname, 'node_modules');

module.exports = {
    entry: {
        bootstraploader: 'bootstrap-loader',
        global: './app/Resources/assets/js/pages/global.js'
    },
    output: {
        filename: '[name].bundle.js',
        path: dest,
        publicPath: isProduction ? '/js/' : 'http://localhost:8092/js/'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: node,
                loader: "babel-loader"
            },
            {
                test: /\.css$/,
                loader: "style-loader!css"
            },
            {
                test: /\.(png|gif|jpe?g|svg?(\?v=[0-9]\.[0-9]\.[0-9])?)$/i,
                loader: 'url-loader?limit=10000'
            },
            {
                test: /\.scss$/,
                loader: "style-loader!css-loader!sass-loader"
            },
            {test: /bootstrap-sass\/assets\/javascripts\//, loader: 'imports-loader?jQuery=jquery'},
            {test: /\.(woff2?|svg)$/, loader: 'url-loader?limit=10000'},
            {test: /\.(ttf|eot)$/, loader: 'file-loader'}
        ]
    },
    plugins: [],
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
    resolve: {
        modules: [
            bower,
            node
        ],
        alias: {
            jquery: path.resolve(bower, 'jquery/src/jquery.js'),
        }
    },
    resolveLoader: { //only used for bc on webpack beta version as long as not all loaders/plugins support "-loader"
        moduleExtensions: ['-loader']
    }
};
