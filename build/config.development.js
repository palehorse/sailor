const merge = require('webpack-merge');
const mix   = require('./../bullets.mix.js');
const ENV   = 'development';

module.exports = (function(mix) {
  mix.forEach(function(config, key) {
    mix[key] = merge(config, {
        mode: ENV,
        watch: true
      });
  });
  return mix;
})(mix);