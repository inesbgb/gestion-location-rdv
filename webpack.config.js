const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enablePostCssLoader()
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .configureLoaderRule('javascript', (loaderRule) => {
        loaderRule.test = /\.(js|jsx)$/;
        loaderRule.exclude = /node_modules/;
    })
    .addAliases({
        '@': path.resolve(__dirname, 'assets')
    });

module.exports = Encore.getWebpackConfig();