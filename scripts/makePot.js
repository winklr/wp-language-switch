const wpPot = require('wp-pot');

wpPot({
    destFile: 'languages/wp-language-switch.pot',
    domain: 'wp-language-switch',
    src: 'src/**/*.php'
});