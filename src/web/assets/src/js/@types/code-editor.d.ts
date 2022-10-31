type MakeMonacoEditorFn = (elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, twigfieldOptions: string, endpointUrl: string, placeholderText: string) => monaco.editor.IStandaloneCodeEditor | undefined;

type SetMonacoEditorLanguageFn = (editor: monaco.editor.IStandaloneCodeEditor, language: string | undefined, elementId: string) => void;

type SetMonacoEditorThemeFn = (editor: monaco.editor.IStandaloneCodeEditor, theme: string | undefined) => void;

interface CodeEditorOptions {
  singleLineEditor?: boolean,

  [key: string]: any;
}
