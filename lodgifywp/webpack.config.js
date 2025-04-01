const path = require('path');

module.exports = {
  entry: {
    admin: './assets/js/admin.js',
    frontend: './assets/js/frontend.js',
    'stripe-payment': './assets/js/stripe-payment.js',
  },
  output: {
    filename: '[name].bundle.js',
    path: path.resolve(__dirname, 'assets/js/dist'),
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
    ],
  },
  optimization: {
    minimize: true,
    splitChunks: {
      cacheGroups: {
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendors',
          chunks: 'all',
        },
      },
    },
  },
}; 