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
  let Craft: any;

  interface Window {
    monaco: any;
    MonacoEnvironment: monaco.Environment;
    twigfieldBaseAssetsUrl: string;
    makeMonacoEditor: MakeMonacoEditorFunction;
  }
}

import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';
import {getCompletionItemsFromEndpoint} from './autocomplete';
import languageIcons from './language-icons'
import '@/css/codefield.pcss';
import 'monaco-editor/esm/vs/base/browser/ui/codicons/codicon/codicon.ttf';

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

self.MonacoEnvironment = {
  globalAPI: true,
  getWorker: function (workerId: string, label: string) {
    const getWorkerModule = (moduleUrl: string, label: string) => {
      return new Worker(self.MonacoEnvironment.getWorkerUrl(moduleUrl, label)!, {
        name: label,
        type: 'module'
      });
    };

    switch (label) {
      case 'json':
        return getWorkerModule('/monaco-editor/esm/vs/language/json/json.worker?worker', label);
      case 'css':
      case 'scss':
      case 'less':
        return getWorkerModule('/monaco-editor/esm/vs/language/css/css.worker?worker', label);
      case 'html':
      case 'handlebars':
      case 'razor':
        return getWorkerModule('/monaco-editor/esm/vs/language/html/html.worker?worker', label);
      case 'typescript':
      case 'javascript':
        return getWorkerModule('/monaco-editor/esm/vs/language/typescript/ts.worker?worker', label);
      default:
        return getWorkerModule('/monaco-editor/esm/vs/editor/editor.worker?worker', label);
    }
  }
};

// Create the editor
function makeMonacoEditor(elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, twigfieldOptions: string, endpointUrl: string, placeholderText = '') {
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
  const options: monaco.editor.IStandaloneEditorConstructionOptions = {...defaultOptions, ...monacoEditorOptions, ...{value: textArea.value}}
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
    cl.add(...classArray);
  }
  // Handle the placeholder text (if any)
  if (placeholderText !== '') {
    const placeholder = document.createElement('div');
    placeholder.id = elementId + '-monaco-editor-placeholder';
    placeholder.innerHTML = placeholderText;
    placeholder.classList.add('monaco-placeholder', 'p-2');
    container.appendChild(placeholder);
  }
  textArea.parentNode.insertBefore(container, textArea);
  textArea.style.display = 'none';
  // Create the Monaco editor
  const editor = monaco.editor.create(container, options);
  console.log(options);
  // When the text is changed in the editor, sync it to the underlying TextArea input
  editor.onDidChangeModelContent(() => {
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
        /* tslint:disable:no-empty */
      });
      // Handle typing the Enter key
      editor.addCommand(monaco.KeyCode.Enter, () => {
        /* tslint:disable:no-empty */
      }, '!suggestWidgetVisible');
      // Handle typing the Tab key
      editor.addCommand(monaco.KeyCode.Tab, () => {
        focusNextElement();
      });
      editor.addCommand(monaco.KeyMod.Shift | monaco.KeyCode.Tab, () => {
        focusPrevElement();
      });
      // Handle Paste
      editor.onDidPaste(() => {
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
    const focusable = getFocusableElements();
    const index = focusable.indexOf(document.activeElement);
    if (index > -1) {
      const nextElement = focusable[index + 1] || focusable[0];
      nextElement.focus();
    }
  }

  function focusPrevElement() {
    const focussable = getFocusableElements();
    const index = focussable.indexOf(document.activeElement);
    if (index > -1) {
      const prevElement = focussable[index - 1] || focussable[focussable.length];
      prevElement.focus();
    }
  }

  function getFocusableElements() {
    let focussable = [];
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

window.monaco = monaco;
console.log(window.monaco);

window.makeMonacoEditor = makeMonacoEditor;

export default makeMonacoEditor;

