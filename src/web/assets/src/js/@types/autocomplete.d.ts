enum AutocompleteTypes {
  TwigExpressionAutocomplete = "TwigExpressionAutocomplete",
  GeneralAutocomplete = "GeneralAutocomplete",
}

interface AutocompleteItem {
  __completions: monaco.languages.CompletionItem,

  [key: string]: AutocompleteItem;
}

interface Autocomplete {
  name: string,
  type: AutocompleteTypes,
  hasSubProperties: boolean,

  [key: string]: AutocompleteItem;
}

type AutocompleteResponse = {[key: string]: Autocomplete};
