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

declare global {
  let __webpack_public_path__: string;
  const Craft: Craft;

  interface Window {
    codeEditorBaseAssetsUrl: string;
    makeMonacoEditor: MakeMonacoEditorFn;
    setMonacoEditorLanguage: SetMonacoEditorLanguageFn;
    setMonacoEditorTheme: SetMonacoEditorThemeFn;
  }
}

// Set the __webpack_public_path__ dynamically so we can work inside of cpresources's hashed dir name
// https://stackoverflow.com/questions/39879680/example-of-setting-webpack-public-path-at-runtime
if (typeof __webpack_public_path__ === 'undefined' || __webpack_public_path__ === '') {
  __webpack_public_path__ = window.codeEditorBaseAssetsUrl;
}

import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';
import {getCompletionItemsFromEndpoint} from './autocomplete';
import {languageIcons, languageIconTitles} from './language-icons'
import {defaultMonacoEditorOptions} from './default-monaco-editor-options'

/**
 * Create a Monaco Editor instance
 *
 * @param {string} elementId - The id of the TextArea or Input element to replace with a Monaco editor
 * @param {string} fieldType - The field's passed in type, used for autocomplete caching
 * @param {string} wrapperClass - Classes that should be added to the field's wrapper <div>
 * @param {IStandaloneEditorConstructionOptions} editorOptions - Monaco editor options
 * @param {string} codefieldOptions - JSON encoded string of arbitrary CodeEditorOptions for the field
 * @param {string} endpointUrl - The controller action endpoint for generating autocomplete items
 * @param {string} placeholderText - Placeholder text to use for the field
 */
function makeMonacoEditor(elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, codefieldOptions: string, endpointUrl: string, placeholderText = ''): monaco.editor.IStandaloneCodeEditor | undefined {
  const textArea = <HTMLInputElement>document.getElementById(elementId);
  const container = document.createElement('div');
  const fieldOptions: CodeEditorOptions = JSON.parse(codefieldOptions);
  const placeholderId = elementId + '-monaco-editor-placeholder';
  // If we can't find the passed in text area or if there is no parent node, return
  if (textArea === null || textArea.parentNode === null) {
    return;
  }
  // Monaco editor defaults, coalesced together
  const monacoEditorOptions: monaco.editor.IStandaloneEditorConstructionOptions = JSON.parse(editorOptions);
  const options: monaco.editor.IStandaloneEditorConstructionOptions = {...defaultMonacoEditorOptions, ...monacoEditorOptions, ...{value: textArea.value}}
  // Make a sibling div for the Monaco editor to live in
  container.id = elementId + '-monaco-editor';
  container.classList.add('monaco-editor','relative', 'box-content', 'monaco-editor-codefield', 'h-full');
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
  // When the text is changed in the editor, sync it to the underlying TextArea input
  editor.onDidChangeModelContent(() => {
    textArea.value = editor.getValue();
  });
  // Add the language icon (if any)
  setMonacoEditorLanguage(editor, options.language, elementId);
  // Set the editor theme
  setMonacoEditorTheme(editor, options.theme);
  // ref: https://github.com/vikyd/vue-monaco-singleline/blob/master/src/monaco-singleline.vue#L150
  if ('singleLineEditor' in fieldOptions && fieldOptions.singleLineEditor) {
    const textModel: monaco.editor.ITextModel | null = editor.getModel();
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
  getCompletionItemsFromEndpoint(fieldType, codefieldOptions, endpointUrl);
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

  /**
   * Move the focus to the next element
   */
  function focusNextElement(): void {
    const focusable = getFocusableElements();
    if (document.activeElement instanceof HTMLFormElement) {
      const index = focusable.indexOf(document.activeElement);
      if (index > -1) {
        const nextElement = focusable[index + 1] || focusable[0];
        nextElement.focus();
      }
    }
  }

  /**
   * Move the focus to the previous element
   */
  function focusPrevElement(): void {
    const focusable = getFocusableElements();
    if (document.activeElement instanceof HTMLFormElement) {
      const index = focusable.indexOf(document.activeElement);
      if (index > -1) {
        const prevElement = focusable[index - 1] || focusable[focusable.length];
        prevElement.focus();
      }
    }
  }

  /**
   * Get the focusable elements in the current form
   *
   * @returns {Array<HTMLElement>} - An array of HTMLElements that can be focusable
   */
  function getFocusableElements(): Array<HTMLElement> {
    let focusable: Array<HTMLElement> = [];
    // add all elements we want to include in our selection
    const focusableElements = 'a:not([disabled]), button:not([disabled]), select:not([disabled]), input[type=text]:not([disabled]), [tabindex]:not([disabled]):not([tabindex="-1"])';
    if (document.activeElement instanceof HTMLFormElement) {
      const activeElement: HTMLFormElement = document.activeElement;
      if (activeElement && activeElement.form) {
        focusable = Array.prototype.filter.call(activeElement.form.querySelectorAll(focusableElements),
          function (element) {
            if (element instanceof HTMLElement) {
              //check for visibility while always include the current activeElement
              return element.offsetWidth > 0 || element.offsetHeight > 0 || element === document.activeElement
            }
            return false;
          });
      }
    }

    return focusable;
  }

  /**
   * Show the placeholder text
   *
   * @param {string} selector - The selector for the placeholder element
   * @param {string} value - The editor field's value (the text)
   */
  function showPlaceholder(selector: string, value: string): void {
    if (value === "") {
      const elem = <HTMLElement>document.querySelector(selector);
      if (elem !== null) {
        elem.style.display = "initial";
      }
    }
  }

  /**
   * Hide the placeholder text
   *
   * @param {string} selector - The selector for the placeholder element
   */
  function hidePlaceholder(selector: string): void {
    const elem = <HTMLElement>document.querySelector(selector);
    if (elem !== null) {
      elem.style.display = "none";
    }
  }

  return editor;
}

/**
 * Set the language for the Monaco editor instance
 *
 * @param {monaco.editor.IStandaloneCodeEditor} editor - the Monaco editor instance
 * @param {string | undefined} language - the editor language
 * @param {string} elementId - the element id used to create the monaco editor from
 */
function setMonacoEditorLanguage(editor: monaco.editor.IStandaloneCodeEditor, language: string | undefined, elementId: string): void {
  const containerId = elementId + '-monaco-editor';
  const iconId = elementId + '-monaco-language-icon';
  const container = document.querySelector('#' + containerId);
  if (container !== null) {
    if (typeof language !== "undefined") {
      const languageIcon = languageIcons[language] ?? '';
      const languageTitle = languageIconTitles[language] ?? '';
      const icon = document.createElement('div');
      monaco.editor.setModelLanguage(editor.getModel()!, language);
      icon.id = iconId;
      // Only add in the icon if one is available
      if (languageIcon !== '') {
        icon.classList.add('monaco-editor-codefield--icon');
        icon.setAttribute('title', Craft.t('twigfield', languageTitle));
        icon.setAttribute('aria-hidden', 'true');
        icon.innerHTML = languageIcon;
      }
      // Replace the icon if it exists, otherwise create a new element
      const currentIcon = container.querySelector('#' + iconId);
      if (currentIcon) {
        container.replaceChild(icon, currentIcon);
      } else {
        container.appendChild(icon);
      }
    }
  }
}

/**
 * Set the theme for the Monaco editor instance
 *
 * @param {monaco.editor.IStandaloneCodeEditor} editor - the Monaco editor instance
 * @param {string | undefined} language - the editor theme
 */
function setMonacoEditorTheme(editor: monaco.editor.IStandaloneCodeEditor, theme: string | undefined): void {
  const editorTheme = theme ?? 'vs';
  editor.updateOptions({theme: editorTheme});
}

// Make the functions globally available
window.makeMonacoEditor = makeMonacoEditor;
window.setMonacoEditorLanguage = setMonacoEditorLanguage;
window.setMonacoEditorTheme = setMonacoEditorTheme;

export {makeMonacoEditor, setMonacoEditorLanguage, setMonacoEditorTheme};

