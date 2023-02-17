var upath = require('upath');
var path = require('path');
var Encore = require('@symfony/webpack-encore');

var build = (name, assetPath, vendorUiPath) => {
  Encore
    // the project directory where compiled assets will be stored
    .setOutputPath(`public/assets/${name}`)
    // the public path used by the web server to access the previous directory
    .setPublicPath(`/assets/${name}`)
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    // uncomment to define the assets of the project
    .addEntry('app', `${assetPath}/js/app.js`)
    // uncomment if you use Sass/SCSS files
    .enableSassLoader((options) => {
      options.additionalData = '@import "~semantic-ui-css/semantic.min.css";';
    })
    .autoProvidejQuery()
    .configureBabel()
    .disableSingleRuntimeChunk()
    .copyFiles({
      from: `${assetPath}/img`,
      to: 'img/[path][name].[ext]',
    }, {
      from: upath.joinSafe(vendorUiPath, 'Resources/private/img'),
      to: 'img/[path][name].[ext]',
    })
    .configureFilenames({
      js: 'js/[name].[fullhash].js',
      css: 'css/[name].[fullhash].css',
    })
    .configureImageRule({
      filename: 'img/[name].[fullhash].[ext]',
    })
    .configureFontRule({
      filename: 'font/[name].[fullhash].[ext]'
    })
  ;

  const config = Encore.getWebpackConfig();
  config.name = name;
  config.resolve.alias = {
    '~': path.resolve(__dirname, '../../'),
    'sylius/ui': vendorUiPath + '/Resources/private',
  };

  Encore.reset();

  return config;
};
module.exports = build;
