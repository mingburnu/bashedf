const mix = require('laravel-mix');
const WebpackShellPluginNext = require('webpack-shell-plugin-next');

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

mix.js('resources/js/app.js', 'public/js').vue()
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss')
    ]);

mix.sass('resources/sass/app.scss', 'public/css');

mix.webpackConfig({
    plugins:
        [
            new WebpackShellPluginNext({
                onBuildStart: {
                    scripts: ['php artisan lang:js public/js/messages.js --quiet'],
                    blocking: true,
                    parallel: false
                }
            })
        ]
});

if (mix.inProduction()) {
    mix.version();
}