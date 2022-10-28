type MakeMonacoEditorFunction = (elementId: string, fieldType: string, wrapperClass: string, editorOptions: string, twigfieldOptions: string, endpointUrl: string, placeholderText: string) => void;

interface CodefieldOptions {
  singleLineEditor?: boolean,

  [key: string]: any;
}
