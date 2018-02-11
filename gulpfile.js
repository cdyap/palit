const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

require('laravel-elixir-webpack-official');
Elixir.webpack.config.module.loaders = [];
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir((mix) => {
    mix.sass('app.scss')
       .version('css/app.css')
       .scripts([
       		'main.js'       		
       	])
       .webpack('app.js');
});
