const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/workTimeClock.js", "public/js/react")
    .js("resources/js/studio.js", "public/js/react")
    .js("resources/js/gigMode.js", "public/js/react")
    .vue();
