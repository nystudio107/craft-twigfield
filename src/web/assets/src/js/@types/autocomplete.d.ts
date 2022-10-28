export const COMPLETION_KEY = '__completions';

enum AutocompleteTypes {
  TwigExpressionAutocomplete = "TwigExpressionAutocomplete",
  GeneralAutocomplete = "GeneralAutocomplete",
}

interface AutocompleteItem {
  [COMPLETION_KEY]: monaco.languages.CompletionItem,

  [key: string]: AutocompleteItem;
}

interface Autocomplete {
  name: string,
  type: string,
  hasSubProperties: boolean,

  [key: string]: AutocompleteItem;
}

type AutocompleteResponse = Array<AutocompleteItem>;
