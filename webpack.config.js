const path = require("path")
const ExtractTextPlugin = require("extract-text-webpack-plugin")
const MiniCssExtractPlugin = require("mini-css-extract-plugin")
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin")
const TerserPlugin = require("terser-webpack-plugin")

module.exports = {
  mode: "production",
  // console logs output, https://webpack.js.org/configuration/stats/
  stats: "errors-warnings",
  performance: {
    // disable warnings hint
    hints: false,
  },
  optimization: {
    // js and css minimizer
    minimize: true,
    minimizer: [
      /* new UglifyJsPlugin({
        test: /\.js(\?.*)?$/i,
        uglifyOptions: {
          output: {
            comments: false,
          },
        },
      }), */
      new TerserPlugin({}),
      new OptimizeCSSAssetsPlugin(),
    ],
  },
  entry: {
    /* "bundle.min": [
      path.resolve(__dirname, "node_modules/bootstrap/dist/css/bootstrap.css"),
      path.resolve(__dirname, "common/themes/mamba/assets/dist/vendor/icofont/icofont.min.css"),
      path.resolve(__dirname, "common/themes/mamba/assets/dist/vendor/boxicons/css/boxicons.min.css"),
      path.resolve(__dirname, "common/themes/mamba/assets/dist/vendor/animate.css/animate.min.css"),
      path.resolve(__dirname, "common/themes/mamba/assets/dist/vendor/venobox/venobox.css"),
      path.resolve(__dirname, "common/themes/mamba/assets/dist/vendor/aos/aos.css"),
      path.resolve(__dirname, "common/themes/mamba/assets/dist/vendor/fancybox/jquery.fancybox.css"),
      path.resolve(__dirname, "common/themes/mamba/assets/dist/css/style.css"),
      path.resolve(__dirname, "frontend/web/js/jssocials/jssocials.css"),
      path.resolve(__dirname, "frontend/web/js/jssocials/jssocials-theme-flat.css"),
      path.resolve(__dirname, "frontend/web/static/fontawesome/css/all.min.css"),
      path.resolve(__dirname, "vendor/bower-asset/font-awesome/css/font-awesome.min.css"),
      path.resolve(__dirname, "frontend/web/css/hover-min.css"),
    ], */
    "app.min.js": [
      path.resolve(__dirname, "web/bundle/app.js")
    ],
  },
  output: {
    filename: "[name]",
    path: path.resolve(__dirname, "web/bundle"),
  },
  module: {
    rules: [
      /*  {
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: "style-loader",
          use: "css-loader",
        }),
      }, */
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, "css-loader"],
      },
      {
        test: /\.s[ac]ss$/i,
        use: [
          // Creates `style` nodes from JS strings
          "style-loader",
          // Translates CSS into CommonJS
          "css-loader",
          // Compiles Sass to CSS
          "sass-loader",
        ],
      },
      {
        test: /\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        use: [
          {
            loader: "file-loader",
            options: {
              // emitFile: false,
              // prevent name become hash
              name: "[name].[ext]",
              // move files
              outputPath: "fonts",
              esModule: false,
            },
          },
        ],
      },
      {
        test: /\.(png|jpe?g|gif)$/i,
        use: [
          {
            loader: "file-loader",
            options: { esModule: false },
          },
        ],
      },
      /* {
        test: /\.(png|jpg|gif)$/i,
        use: [
          {
            loader: "url-loader",
            options: {
              limit: 8192,
            },
          },
        ],
      }, */
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "[name].css",
    }),
  ],
}
