const mix = require('laravel-mix')
const path = require('path')

mix.webpackConfig({
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/assets/js'),
    },
    fallback: {
      "path": require.resolve("path-browserify")
    }
  },
})

mix
  .js('resources/assets/js/app.js', 'public/assets/js/')
  .vue({
    version: 2,
    extractVueStyles: true,
  })
  .postCss('resources/assets/css/style.css', 'public/assets/css/')
 



  
if (!mix.inProduction()) {
  mix
    .webpackConfig({
      devtool: 'source-map',
    })
    .sourceMaps()
} else {
  mix.version()
}
