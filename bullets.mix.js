const bullets = require('./build/bullets');

var commonCss = [
    '../../node_modules/bootstrap/dist/css/bootstrap.css',
    'common/common.css'
];

bullets.css(commonCss.concat(['error.css']), 'error.css');

module.exports = (function(bullets) {
    return bullets.getConfig();
})(bullets);