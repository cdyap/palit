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
    mix.sass(['app.scss','dropzone.scss'])
       .version('css/app.css')
       // .scripts([
       // 		'jquery.min.js',
       //            'popper.js',
       // 		'bootstrap.min.js',
       // 		'datepicker.js',
       // 		'dropzone.js',
       // 		'main.js'
       // 	])
       // .webpack('app.js');
       .scripts([
            // list your other npm packages here
            'jquery/dist/jquery.min.js',     
            'popper.js/dist/umd/popper.min.js',     
            'bootstrap/dist/js/bootstrap.min.js',     
            '@fengyuanchen/datepicker/dist/datepicker.js',     
            'dropzone/dist/dropzone.js',     
            'frenzyapp/turbolinks.js',     
            'onmount/index.js',     
       ],
       'public/js/vendor.js', // 2nd param is the output file
       'node_modules')        // 3rd param is saying "look in /node_modules/ for these scripts"
      .scripts([
            'main.js'       // your custom js file located in default location: /resources/assets/js/
       ], 'public/js/app.js') // looks in default location since there's no 3rd param
       .version([             // optionally append versioning string to filename
            'js/vendor.js',    // compiled files will be in /public/build/js/
            'js/app.js'
      ]).browserify('app.js');
});
