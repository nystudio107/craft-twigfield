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
    'code-editor': [
      '@/js/code-editor.ts',
      '@/css/code-editor.pcss',
      'monaco-editor/esm/vs/base/browser/ui/codicons/codicon/codicon.ttf'
    ],
  },
  extensions: ['.ts', '.js', '.vue', '.json'],
  name: 'code-editor',
  paths: {
    dist: path.resolve('../src/web/assets/dist/'),
  },
  urls: {
    publicPath: () => process.env.PUBLIC_PATH || '',
  },
};
