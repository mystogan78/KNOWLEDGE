const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .setOutputPath('public/build/')   // <-- IMPORTANT
  .setPublicPath('/build')          // <-- IMPORTANT

  .addEntry('app', './assets/app.js') // <-- UNE SEULE entrée

  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .configureBabelPresetEnv(config => {
    config.useBuiltIns = 'usage';
    config.corejs = '3.38';
  })
  .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();

