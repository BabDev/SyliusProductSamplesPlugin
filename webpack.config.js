const path = require('path');
const Encore = require('@symfony/webpack-encore');
const pluginName = 'product-samples';

const getConfig = (pluginName, type) => {
    Encore.reset();

    Encore
        .setOutputPath(`public/build/babdev/${pluginName}/${type}/`)
        .setPublicPath(`/build/babdev/${pluginName}/${type}/`)
        .addEntry(`babdev-${pluginName}-${type}`, path.resolve(__dirname, `./src/Resources/assets/${type}/entry.js`))
        .disableSingleRuntimeChunk()
        .cleanupOutputBeforeBuild()
        .enableSourceMaps(!Encore.isProduction())
        .enableSassLoader();

    const config = Encore.getWebpackConfig();
    config.name = `babdev-${pluginName}-${type}`;

    return config;
}

Encore
    .setOutputPath('src/Resources/public/')
    .setPublicPath('/public/')
    .addEntry(`babdev-${pluginName}-shop`, path.resolve(__dirname, './src/Resources/assets/shop/entry.js'))
    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()
    .enableSassLoader();

const distConfig = Encore.getWebpackConfig();
distConfig.name = 'product-samples-plugin-dist';

Encore.reset();

const shopConfig = getConfig(pluginName, 'shop')

module.exports = [shopConfig, distConfig];
