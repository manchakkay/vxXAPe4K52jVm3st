const mix = require('laravel-mix');
const webpack = require('webpack');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    // JS
    // .js('resources/js/app.js', 'public/js')
    .js('resources/js/home.js', 'public/js/home.js')
    .js('resources/js/home_inner.js', 'public/js/home_inner.js')
    .js('resources/js/admin_app.js', 'public/js/admin_app.js')
    // Vue
    .vue({ version: 3 })
    // Admin Styles
    .sass('resources/sass/admin.scss', 'public/css')
    .sass('resources/sass/redact.scss', 'public/css')
    // Admin Pages Styles
    .sass('resources/sass/admin/pages/news.scss', 'public/css/admin/pages')
    .sass('resources/sass/admin/pages/pages.scss', 'public/css/admin/pages')
    .sass('resources/sass/admin/pages/files.scss', 'public/css/admin/pages')
    .sass('resources/sass/admin/pages/gallery_photos.scss', 'public/css/admin/pages')

    // User Styles
    .sass('resources/sass/user.scss', 'public/css')
    .sass('resources/sass/user/user_noscript.scss', 'public/css')
    .sass('resources/sass/user/user_960.scss', 'public/css')
    .sass('resources/sass/user/user_640.scss', 'public/css')
    .sass('resources/sass/user/user_480.scss', 'public/css')
    .sass('resources/sass/user/user_320.scss', 'public/css')
    // Inner
    .sass('resources/sass/user_inner.scss', 'public/css')
    // Login Styles
    .sass('resources/sass/login.scss', 'public/css')
    // Resources
    .copyDirectory('resources/fonts', 'public/fonts')
    .copyDirectory('resources/assets', 'public/assets')
    // Async Scripts for popups
    .copyDirectory('resources/js/async_scripts', 'public/js/async_scripts/')
    .minify('resources/js/async_scripts/createNews.js', 'public/js/async_scripts/createNews.js')
    .minify('resources/js/async_scripts/editNews.js', 'public/js/async_scripts/editNews.js')
    .minify('resources/js/async_scripts/createPage.js', 'public/js/async_scripts/createPage.js')
    .minify('resources/js/async_scripts/editPage.js', 'public/js/async_scripts/editPage.js')
    .minify('resources/js/async_scripts/createFile.js', 'public/js/async_scripts/createFile.js')
    .minify('resources/js/async_scripts/createGalleryPhoto.js', 'public/js/async_scripts/createGalleryPhoto.js')
    // Vue Config
    .webpackConfig({
        plugins: [
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: true,
                __VUE_PROD_DEVTOOLS__: false
            })
        ],
        stats: {
            // children: true,
        },
    });
