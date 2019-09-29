let assets_dir = './assets';

let Encore = require('@symfony/webpack-encore');

Encore
// directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath(Encore.isProduction() ? '/build' : 'http://swapp:8083/build/')

    // .setOutputPath()

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // will output as web/build/app.js
    .addEntry('app', assets_dir + '/js/pages/global.js')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    .setManifestKeyPrefix('build/')

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enable vue-load
    .enableVueLoader()
;

// export the final configuration
config = Encore.getWebpackConfig();

if (config.devServer) {
    config.devServer.host = '0.0.0.0';
    config.devServer.https = Encore.isProduction();
    config.devServer.port = '8083';
    config.devServer.public = "swapp:8083";
}

module.exports = config;
