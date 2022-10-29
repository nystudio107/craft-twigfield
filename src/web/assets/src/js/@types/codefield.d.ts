type MakeMonacoEditorFunction = (elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, twigfieldOptions: string, endpointUrl: string, placeholderText: string) => monaco.editor.IStandaloneCodeEditor | undefined;

interface CodefieldOptions {
  singleLineEditor?: boolean,

  [key: string]: any;
}
