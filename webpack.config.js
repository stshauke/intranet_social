// webpack.config.js
const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Répertoire où sera compilé le JS/CSS
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Point d'entrée principal
    .addEntry('app', './assets/app.js')

    // Stimulus bridge (optionnel mais utile)
    .enableStimulusBridge('./assets/controllers.json')

    // Optimisations
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // Babel pour la compatibilité navigateurs
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // SASS / SCSS
    .enableSassLoader()

    // ✅ Fournir automatiquement jQuery pour tous les fichiers JS qui en ont besoin
    .autoProvidejQuery()

    // ✅ Fournir jQuery globalement à tous les modules, y compris Bootstrap et Select2
    .addPlugin(new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    }))
;

module.exports = Encore.getWebpackConfig();
