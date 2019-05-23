const path = require('path');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");

module.exports = {
  entry: {
    'core': './Resources/assets/js/core.js',
    'login': './Resources/assets/js/login.js',
    'reminderLatest': './Resources/assets/js/reminderLatest.js',
    'sortSubmit': './Resources/assets/js/sortSubmit.js',
    'journalQuickview': './Resources/assets/js/journalQuickview.js',
    'processFilters': './Resources/assets/js/processFilters.js',
    'processStatusForm': './Resources/assets/js/processStatusForm.js',
    'globalSearch': './Resources/assets/js/globalSearch.js',
    'searchPage': './Resources/assets/js/searchPage.js'
  },
  output: {
    path: path.resolve('./Resources/public/build'),
    filename: "[name].js",
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          "css-loader",
          'sass-loader'
        ]
      },
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
      {
        test: require.resolve('jquery'),
        use: [{
          loader: 'expose-loader',
          options: 'jQuery'
        },{
          loader: 'expose-loader',
          options: '$'
        }]
      }
    ]
  },
  optimization: {
    minimizer: [
      new OptimizeCSSAssetsPlugin({
        cssProcessorPluginOptions: {
          preset: ['default', { discardComments: { removeAll: true } }],
        },
      }),
      new UglifyJsPlugin({
        uglifyOptions: {
          output: {
            comments: false,
          },
        },
      })
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "[name].css",
      chunkFilename: "[id].css"
    })
  ]
};
