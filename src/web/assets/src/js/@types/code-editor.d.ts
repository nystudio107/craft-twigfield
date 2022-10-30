type MakeMonacoEditorFunction = (elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, twigfieldOptions: string, endpointUrl: string, placeholderText: string) => monaco.editor.IStandaloneCodeEditor | undefined;

interface CodeEditorOptions {
  singleLineEditor?: boolean,

  [key: string]: any;
}
