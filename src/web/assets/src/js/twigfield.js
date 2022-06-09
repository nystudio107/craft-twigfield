/**
 * Twigfield Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
// Set the __webpack_public_path__ dynamically so we can work inside of cpresources's hashed dir name
// https://stackoverflow.com/questions/39879680/example-of-setting-webpack-public-path-at-runtime
if (typeof __webpack_public_path__ !== 'string' || __webpack_public_path__ === '') {
  __webpack_public_path__ = window.twigfieldBaseAssetsUrl;
}

console.log(__webpack_public_path__);
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';

/* For now, use the default theme

import editorTheme from 'monaco-themes/themes/Night Owl.json';

monaco.editor.defineTheme('night-owl', editorTheme);
monaco.editor.setTheme('night-owl');
*/

// Create the editor
function makeMonacoEditor(elementId, fieldType) {
  const textArea = document.getElementById(elementId);
  let container = document.createElement('div');
  // Make a sibling div for the Monaco editor to live in
  container.id = elementId + '-monaco-editor';
  container.classList.add('p-2', 'box-content', 'monaco-editor-background-frame', 'w-full', 'h-full');
  container.tabIndex = 0;
  textArea.parentNode.insertBefore(container, textArea);
  textArea.style.display = 'none';
  // Create the Monaco editor
  let editor = monaco.editor.create(container, {
    value: textArea.value,
    language: 'twig',
    automaticLayout: true,
    // Disable sidebar line numbers
    lineNumbers: 'off',
    glyphMargin: false,
    folding: false,
    // Undocumented see https://github.com/Microsoft/vscode/issues/30795#issuecomment-410998882
    lineDecorationsWidth: 0,
    lineNumbersMinChars: 0,
    // Disable the current line highlight
    renderLineHighlight: false,
    wordWrap: true,
    scrollBeyondLastLine: false,
    scrollbar: {
      vertical: 'hidden',
      horizontal: 'auto',
    },
    fontSize: 14,
    fontFamily: 'SFMono-Regular, Consolas, "Liberation Mono", Menlo, Courier, monospace',
    minimap: {
      enabled: false
    },
  });
  // When the text is changed in the editor, sync it to the underlying TextArea input
  editor.onDidChangeModelContent((event) => {
    textArea.value = editor.getValue();
  });
  // Handle keyboard shortcuts too via beforeSaveShortcut
  Craft.cp.on('beforeSaveShortcut', () => {
    textArea.value = editor.getValue();
  });
  // Get the autocompletion items
  if (typeof window.monacoAutocompleteItemsAdded !== 'undefined') {
    //getCompletionItemsFromEndpoint(fieldType);
    window.monacoAutocompleteItemsAdded = true;
  }
  // Custom resizer to always keep the editor full-height, without needing to scroll
  let ignoreEvent = false;
  const updateHeight = () => {
    const width = editor.getLayoutInfo().width;
    const contentHeight = Math.min(1000, editor.getContentHeight());
    //container.style.width = `${width}px`;
    container.style.height = `${contentHeight}px`;
    try {
      ignoreEvent = true;
      editor.layout({width, height: contentHeight});
    } finally {
      ignoreEvent = false;
    }
  };
  editor.onDidContentSizeChange(updateHeight);
  updateHeight();
}

window.makeMonacoEditor = makeMonacoEditor;

export default makeMonacoEditor;
