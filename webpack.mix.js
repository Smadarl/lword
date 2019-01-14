//module.exports = {
//   resolve: {
//       alias: {
//             // 'vue$': 'vue/dist/vue.esm.js' // 'vue/dist/vue.common.js' for webpack 1
//             'vue$': 'vue/dist/vue.common.js' // for webpack 1
//       }
//   }
//}

let mix = require('laravel-mix');

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

mix.js('resources/assets/js/game.js', 'public/js')
    .js('resources/assets/js/game-list.js', 'public/js')
    .js('resources/assets/js/profile.js', 'public/js')
    .js('resources/assets/js/friends.js', 'public/js')
    .js('resources/assets/js/pending.js', 'public/js')
    .js('resources/assets/js/create-game.js', 'public/js')
    .js('resources/assets/js/test.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css');
