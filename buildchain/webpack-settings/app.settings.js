// app.settings.js

// node modules
require('dotenv').config();
const path = require('path');

// settings
module.exports = {
  alias: {
    '@': path.resolve('../src/web/assets/src'),
  },
  copyright: '©2022 nystudio107.com',
  entry: {
    'codefield-editor': [
      '@/js/codefield.ts',
      '@/css/codefield.pcss',
      'monaco-editor/esm/vs/base/browser/ui/codicons/codicon/codicon.ttf'
    ],
  },
  extensions: ['.ts', '.js', '.vue', '.json'],
  name: 'codefield',
  paths: {
    dist: path.resolve('../src/web/assets/dist/'),
  },
  urls: {
    publicPath: () => process.env.PUBLIC_PATH || '',
  },
};
