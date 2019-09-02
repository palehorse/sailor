const bullets = require('./build/bullets');

/*
bullets.js(['app.js', 'your.js'], 'your-minified.js')
       .css(['../../node_modules/bootstrap/dist/css/bootstrap.css', 'your.css'], 'your-minified.css');
*/
module.exports = (function(bullets) {
    return bullets.getConfig();
})(bullets);