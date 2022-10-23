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

import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';
import {getCompletionItemsFromEndpoint} from '@/js/autocomplete.js';

// The default EditorOptions for the Monaco editor instance
// ref: https://microsoft.github.io/monaco-editor/api/enums/monaco.editor.EditorOption.html
const defaultOptions = {
  language: 'twig',
  theme: 'vs',
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
    alwaysConsumeMouseWheel: false,
    handleMouseWheel: false,
  },
  fontSize: 14,
  fontFamily: 'SFMono-Regular, Consolas, "Liberation Mono", Menlo, Courier, monospace',
  minimap: {
    enabled: false
  },
};

// Create the editor
function makeMonacoEditor(elementId, fieldType, wrapperClass, editorOptions, twigfieldOptions, endpointUrl, placeholderText = '') {
  const textArea = document.getElementById(elementId);
  let container = document.createElement('div');
  let fieldOptions = JSON.parse(twigfieldOptions);
  let placeholderId = elementId + '-monaco-editor-placeholder';
  // Make a sibling div for the Monaco editor to live in
  container.id = elementId + '-monaco-editor';
  container.classList.add('p-2', 'relative', 'box-content', 'monaco-editor-twigfield-icon', 'h-full');
  if (wrapperClass !== '') {
    const cl = container.classList;
    const classArray = wrapperClass.trim().split(/\s+/);
    cl.add.apply(cl, classArray);
  }
  container.tabIndex = 0;
  if (placeholderText !== '') {
    let placeholder = document.createElement('div');
    placeholder.id = elementId + '-monaco-editor-placeholder';
    placeholder.innerHTML = placeholderText;
    placeholder.classList.add('monaco-placeholder', 'p-2');
    container.appendChild(placeholder);
  }
  textArea.parentNode.insertBefore(container, textArea);
  textArea.style.display = 'none';
  // Create the Monaco editor
  let options = {...defaultOptions, ...JSON.parse(editorOptions), ...{value: textArea.value}}
  let editor = monaco.editor.create(container, options);
  // When the text is changed in the editor, sync it to the underlying TextArea input
  editor.onDidChangeModelContent((event) => {
    textArea.value = editor.getValue();
  });
  // ref: https://github.com/vikyd/vue-monaco-singleline/blob/master/src/monaco-singleline.vue#L150
  if ('singleLineEditor' in fieldOptions && fieldOptions.singleLineEditor) {
    const textModel = editor.getModel();
    // Remove multiple spaces & tabs
    const text = textModel.getValue();
    textModel.setValue(text.replace(/\s\s+/g, ' '));
    // Handle the Find command
    editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyF, () => {
    });
    // Handle typing the Enter key
    editor.addCommand(monaco.KeyCode.Enter, () => {
    }, '!suggestWidgetVisible');
    // Handle Paste
    editor.onDidPaste((e) => {
      // multiple rows will be merged to single row
      let newContent = '';
      const lineCount = textModel.getLineCount();
      // remove all line breaks
      for (let i = 0; i < lineCount; i += 1) {
        newContent += textModel.getLineContent(i + 1);
      }
      // Remove multiple spaces & tabs
      newContent = newContent.replace(/\s\s+/g, ' ');
      textModel.setValue(newContent);
      editor.setPosition({column: newContent.length + 1, lineNumber: 1});
    })
  }
  // Get the autocompletion items
  getCompletionItemsFromEndpoint(fieldType, twigfieldOptions, endpointUrl);
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
  // Handle the placeholder
  if (placeholderText !== '') {
    showPlaceholder('#' + placeholderId, editor.getValue());
    editor.onDidBlurEditorWidget(() => {
      showPlaceholder('#' + placeholderId, editor.getValue());
    });
    editor.onDidFocusEditorWidget(() => {
      hidePlaceholder('#' + placeholderId);
    });
  }

  function showPlaceholder(selector, value) {
    if (value === "") {
      document.querySelector(selector).style.display = "initial";
    }
  }

  function hidePlaceholder(selector) {
    document.querySelector(selector).style.display = "none";
  }
}

window.makeMonacoEditor = makeMonacoEditor;

export default makeMonacoEditor;
