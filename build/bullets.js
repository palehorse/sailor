const {ASSETS, DIST, SRC}     = require('./paths');
const ExtractTextPlugin       = require("extract-text-webpack-plugin");
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

module.exports = (function(SRC) {
    var configs  = [];
    var entry = {};
    function bundleJs(jsSrc, jsDist) {
        jsSrc.forEach(function(path, key) {
            jsSrc[key] = SRC+'/js/'+jsSrc[key];
        });

        configs.push({
            entry: jsSrc,
            output: {
                filename: jsDist,
                path: DIST+'/js',
                publicPath: ASSETS
            }
        });
        return this;
    }

    function bundleCss(cssSrc, cssDist) {
        cssSrc.forEach(function(path, key) {
            cssSrc[key] = SRC+'/css/'+cssSrc[key];
        });
        configs.push({
            entry: cssSrc,
            output: {
                filename: cssDist,
                path: DIST+'/css',
                publicPath: ASSETS
            },
            module:{
                rules:[{
                    test:/\.css$/,
                    use: ExtractTextPlugin.extract({
                        fallback: "style-loader",
                        use: "css-loader"
                    })
                }]
            },
            plugins: [
              new ExtractTextPlugin(cssDist),
              new OptimizeCssAssetsPlugin({
                cssProcessor: require('cssnano'),
                cssProcessorPluginOptions: {
                  preset: ['default', { discardComments: { removeAll: true } }],
                },
                canPrint: true
              })
            ]
        });
        return this;
    }

    function getConfig() {
        return configs;
    }

    return {
        js: bundleJs,
        css: bundleCss,
        getConfig: getConfig
    };
})(SRC);