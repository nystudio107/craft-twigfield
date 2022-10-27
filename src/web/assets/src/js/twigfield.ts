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

type MakeMonacoEditorFunction = (elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, twigfieldOptions: string, endpointUrl: string, placeholderText: string) => void;

declare global {
  var __webpack_public_path__: string;
  var Craft: any;
  interface Window {
    twigfieldBaseAssetsUrl: string;
    makeMonacoEditor: MakeMonacoEditorFunction;
  }
}

// Set the __webpack_public_path__ dynamically so we can work inside of cpresources's hashed dir name
// https://stackoverflow.com/questions/39879680/example-of-setting-webpack-public-path-at-runtime
if (typeof __webpack_public_path__ !== 'string' || __webpack_public_path__ === '') {
  __webpack_public_path__ = window.twigfieldBaseAssetsUrl;
}

import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';
import {getCompletionItemsFromEndpoint} from './autocomplete';
import languageIcons from './language-icons'

// The default EditorOptions for the Monaco editor instance
// ref: https://microsoft.github.io/monaco-editor/api/enums/monaco.editor.EditorOption.html
const defaultOptions: monaco.editor.IStandaloneEditorConstructionOptions = {
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
  renderLineHighlight: 'none',
  wordWrap: 'on',
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
function makeMonacoEditor(elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, twigfieldOptions: string, endpointUrl: string, placeholderText: string = '') {
  const textArea = <HTMLInputElement>document.getElementById(elementId);
  const container = document.createElement('div');
  const fieldOptions = JSON.parse(twigfieldOptions);
  const placeholderId = elementId + '-monaco-editor-placeholder';
  // If we can't find the passed in text area or if there is no parent node, return
  if (textArea === null || textArea.parentNode === null) {
    return;
  }
  // Monaco editor defaults, coalesced together
  const monacoEditorOptions: monaco.editor.IStandaloneEditorConstructionOptions = JSON.parse(editorOptions);
  let options: monaco.editor.IStandaloneEditorConstructionOptions = {...defaultOptions, ...monacoEditorOptions, ...{value: textArea.value}}
  // Make a sibling div for the Monaco editor to live in
  container.id = elementId + '-monaco-editor';
  container.classList.add('relative', 'box-content', 'monaco-editor-twigfield', 'h-full');
  // Add the icon in, if there is one
  const iconHtml = typeof options.language === "undefined" ? null : languageIcons[options.language];
  if (iconHtml) {
    const icon = document.createElement('div');
    icon.classList.add('monaco-editor-twigfield--icon');
    icon.setAttribute('title', Craft.t('twigfield', 'Twig code is supported.'));
    icon.setAttribute('aria-hidden', 'true');
    icon.innerHTML = iconHtml;
    container.appendChild(icon);
  }
  // Apply any passed in classes to the wrapper div
  if (wrapperClass !== '') {
    const cl = container.classList;
    const classArray = wrapperClass.trim().split(/\s+/);
    cl.add.apply(cl, classArray);
  }
  // Handle the placeholder text (if any)
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
  let editor = monaco.editor.create(container, options);
  // When the text is changed in the editor, sync it to the underlying TextArea input
  editor.onDidChangeModelContent((event) => {
    textArea.value = editor.getValue();
  });
  // ref: https://github.com/vikyd/vue-monaco-singleline/blob/master/src/monaco-singleline.vue#L150
  if ('singleLineEditor' in fieldOptions && fieldOptions.singleLineEditor) {
    const textModel = editor.getModel();
    if (textModel !== null) {
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
    // add all elements we want to include in our selection
    const focussableElements = 'a:not([disabled]), button:not([disabled]), select:not([disabled]), input[type=text]:not([disabled]), [tabindex]:not([disabled]):not([tabindex="-1"])';
    const activeElement: HTMLFormElement = <HTMLFormElement>document.activeElement;
    if (activeElement && activeElement.form) {
      focussable = Array.prototype.filter.call(activeElement.form.querySelectorAll(focussableElements),
        function (element) {
          //check for visibility while always include the current activeElement
          return element.offsetWidth > 0 || element.offsetHeight > 0 || element === document.activeElement
        });
    }

    return focussable;
  }

  function showPlaceholder(selector: string, value: string) {
    if (value === "") {
      const elem = <HTMLElement>document.querySelector(selector);
      if (elem !== null) {
        elem.style.display = "initial";
      }
    }
  }

  function hidePlaceholder(selector: string) {
    const elem = <HTMLElement>document.querySelector(selector);
    if (elem !== null) {
      elem.style.display = "none";
    }
  }
}

window.makeMonacoEditor = makeMonacoEditor;

export default makeMonacoEditor;

