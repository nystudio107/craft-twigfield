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
  tabIndex: 0,
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
  container.classList.add('relative', 'box-content', 'monaco-editor-twigfield', 'h-full');
  const icon = document.createElement('div');
  icon.classList.add('monaco-editor-twigfield--icon');
  icon.setAttribute('title', Craft.t('twigfield', 'Twig code is supported.'));
  icon.setAttribute('aria-hidden', 'true');
  icon.innerHTML = `<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 320 320" style="enable-background:new 0 0 320 320;" xml:space="preserve">
<style type="text/css">.st0{fill:currentcolor;}</style>
<g>
\t<path class="st0" d="M128,35.6c-17.7,0-32,15.9-32,35.6v35.6c0,29.5-21.5,53.3-48,53.3c26.5,0,48,23.9,48,53.3v35.6
\t\tc0,19.6,14.3,35.6,32,35.6V320H96c-35.3,0-64-31.9-64-71.1v-35.6c0-19.6-14.3-35.6-32-35.6v-35.6c17.7,0,32-15.9,32-35.6V71.1
\t\tC32,31.9,60.7,0,96,0h32V35.6L128,35.6z"/>
\t<path class="st0" d="M320,177.8c-17.7,0-32,15.9-32,35.6v35.6c0,39.2-28.7,71.1-64,71.1h-32v-35.6c17.7,0,32-15.9,32-35.6v-35.6
\t\tc0-29.5,21.5-53.3,48-53.3c-26.5,0-48-23.9-48-53.3V71.1c0-19.6-14.3-35.6-32-35.6V0h32c35.3,0,64,31.9,64,71.1v35.6
\t\tc0,19.6,14.3,35.6,32,35.6V177.8L320,177.8z"/>
</g>
</svg>`;
  container.appendChild(icon);
  if (wrapperClass !== '') {
    const cl = container.classList;
    const classArray = wrapperClass.trim().split(/\s+/);
    cl.add.apply(cl, classArray);
  }
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
    console.log(fieldOptions);
    // Remove multiple spaces & tabs
    const text = textModel.getValue();
    textModel.setValue(text.replace(/\s\s+/g, ' '));
    // Handle the Find command
    editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyF, () => {
    });
    // Handle typing the Enter key
    editor.addCommand(monaco.KeyCode.Enter, () => {
    }, '!suggestWidgetVisible');
    // Handle typing the Tab key
    editor.addCommand(monaco.KeyCode.Tab, () => {
      focusNextElement();
    });
    editor.addCommand(monaco.KeyMod.Shift | monaco.KeyCode.Tab, () => {
      focusPrevElement();
    });
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

  function focusNextElement() {
    var focussable = getFocusableElements();
    var index = focussable.indexOf(document.activeElement);
    if (index > -1) {
      var nextElement = focussable[index + 1] || focussable[0];
      nextElement.focus();
    }
  }

  function focusPrevElement() {
    var focussable = getFocusableElements();
    var index = focussable.indexOf(document.activeElement);
    if (index > -1) {
      var prevElement = focussable[index - 1] || focussable[focussable.length];
      prevElement.focus();
    }
  }

  function getFocusableElements() {
    var focussable = [];
    //add all elements we want to include in our selection
    var focussableElements = 'a:not([disabled]), button:not([disabled]), select:not([disabled]), input[type=text]:not([disabled]), [tabindex]:not([disabled]):not([tabindex="-1"])';
    if (document.activeElement && document.activeElement.form) {
      focussable = Array.prototype.filter.call(document.activeElement.form.querySelectorAll(focussableElements),
        function (element) {
          //check for visibility while always include the current activeElement
          return element.offsetWidth > 0 || element.offsetHeight > 0 || element === document.activeElement
        });
    }

    return focussable;
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

