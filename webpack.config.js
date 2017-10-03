let assets_dir = './app/Resources/assets';

let Encore = require('@symfony/webpack-encore');

Encore
// directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath(Encore.isProduction() ? '/build' : 'https://swapp:8082/build/')

    // .setOutputPath()

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // will output as web/build/app.js
    .addEntry('app', assets_dir + '/js/pages/global.js')

    // will output as web/build/global.css
    .addStyleEntry('global', assets_dir + '/css/main.scss')

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    .setManifestKeyPrefix('build/')

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
;

// export the final configuration
config = Encore.getWebpackConfig();

if (config.devServer) {
    config.devServer.host = '0.0.0.0';
    config.devServer.https = true;
    config.devServer.port = '8082';
    config.devServer.public = "swapp:8082";
}

module.exports = config;
