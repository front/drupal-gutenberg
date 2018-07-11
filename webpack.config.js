const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
  entry: './client_src/index.js',
  output: {
    filename: 'js/[name].bundle.js',
    path: path.resolve(__dirname, 'dist')
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: 'babel-loader',
      },
      {
        test: /\.jsx$/,
        exclude: /node_modules/,
        use: 'babel-loader',
      },
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader', 'sass-loader']
        })
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  plugins: [
    new CleanWebpackPlugin(['dist']),
    new ExtractTextPlugin('css/main.css'),
    new CopyWebpackPlugin([
      {
        from : 'node_modules/tinymce/plugins/',
        to: 'js/plugins/'
      },
      {
        from : 'node_modules/tinymce/themes/',
        to: 'js/themes/'
      },
      {
        from : 'node_modules/@frontkom/gutenberg/build/css/style.css',
        to: 'css/style.css'
      },
      {
        from : 'node_modules/@frontkom/gutenberg/build/css/core-blocks/style.css',
        to: 'css/core-blocks/style.css'
      },
      {
        from : 'node_modules/@frontkom/gutenberg/build/css/core-blocks/edit-blocks.css',
        to: 'css/core-blocks/edit-blocks.css'
      },
      {
        from : 'node_modules/@frontkom/gutenberg/build/css/core-blocks/theme.css',
        to: 'css/core-blocks/theme.css'
      }
    ])      
  ],
  optimization: {
    // Not used for now. Vendor css files are imported on
    // the main css file.
    splitChunks: {
      cacheGroups: {
        vendor: {
          chunks: 'initial',
          test: path.resolve(__dirname, 'node_modules'),
          name: 'vendor',
          enforce: true
        }
      }
    }
  },  
  mode: 'production'
};