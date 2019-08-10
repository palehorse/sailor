const bullets = require('./build/bullets');

bullets.js(['app.js', 'invoice.js'], 'invoice.js')
       .css(['../../node_modules/bootstrap/dist/css/bootstrap.css', 'invoice.css'], 'invoice.css');

module.exports = (function(bullets) {
    return bullets.getConfig();
})(bullets);