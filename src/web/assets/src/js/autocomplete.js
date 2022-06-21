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
const COMPLETION_KEY = '__completions';

/**
 * Get the last item from the array
 *
 * @param arr
 * @returns {*}
 */
function getLastItem(arr) {
  return arr[arr.length - 1];
}

/**
 * Register completion items with the Monaco editor, for the Twig language
 *
 * @param completionItems array of completion items, with sub-properties in `COMPLETION_KEY`
 * @param autocompleteType the type of autocomplete, either `TwigExpressionAutocomplete` or `GeneralAutocomplete`
 * @param hasSubProperties where the autocomplete has sub-properties, and should be parsed as such
 */
function addCompletionItemsToMonaco(completionItems, autocompleteType, hasSubProperties) {
  monaco.languages.registerCompletionItemProvider('twig', {
    triggerCharacters: ['.', '('],
    provideCompletionItems: function (model, position, token) {
      let result = [];
      let currentItems = completionItems;
      // Get the last word the user has typed
      const currentLine = model.getValueInRange({
        startLineNumber: position.lineNumber,
        startColumn: 0,
        endLineNumber: position.lineNumber,
        endColumn: position.column
      });
      let inTwigExpression = true;
      // Ensure we're inside of a Twig expression
      if (currentLine.lastIndexOf('{') === -1) {
        inTwigExpression = false;
      }
      const startExpression = currentLine.substring(currentLine.lastIndexOf('{'));
      if (startExpression.indexOf('}') !== -1) {
        inTwigExpression = false;
      }
      // We are not in a Twig expression, and this is a TwigExpressionAutocomplete, return nothing
      if (!inTwigExpression && autocompleteType === 'TwigExpressionAutocomplete') {
        return null;
      }
      // Get the current word we're typing
      const currentWords = currentLine.replace("\t", "").split(" ");
      let currentWord = currentWords[currentWords.length - 1];
      // If the current word includes { or ( or >, split on that, too, to allow the autocomplete to work in nested functions and HTML tags
      if (currentWord.includes('{')) {
        currentWord = getLastItem(currentWord.split('{'));
      }
      if (currentWord.includes('(')) {
        currentWord = getLastItem(currentWord.split('('));
      }
      if (currentWord.includes('>')) {
        currentWord = getLastItem(currentWord.split('>'));
      }
      const isSubProperty = currentWord.charAt(currentWord.length - 1) === ".";
      // If we're in a sub-property (following a .) don't present non-TwigExpressionAutocomplete items
      if (isSubProperty && autocompleteType !== 'TwigExpressionAutocomplete') {
        return null;
      }
      // We are in a Twig expression, handle TwigExpressionAutocomplete by walking through the properties
      if (inTwigExpression && autocompleteType === 'TwigExpressionAutocomplete') {
        // If the last character typed is a period, then we need to look up a sub-property of the completionItems
        if (isSubProperty) {
          // If we're in a sub-property, and this autocomplete doesn't have sub-properties, don't return its items
          if (!hasSubProperties) {
            return null;
          }
          // Is a sub-property, get a list of parent properties
          const parents = currentWord.substring(0, currentWord.length - 1).split(".");
          if (typeof completionItems[parents[0]] !== 'undefined') {
            currentItems = completionItems[parents[0]];
            // Loop through all the parents to traverse the completion items and find the current one
            for (let i = 1; i < parents.length; i++) {
              if (currentItems.hasOwnProperty(parents[i])) {
                currentItems = currentItems[parents[i]];
              } else {
                return result;
              }
            }
          }
        }
      }
      // Get all the child properties
      if (typeof currentItems !== 'undefined') {
        for (let item in currentItems) {
          if (currentItems.hasOwnProperty(item) && !item.startsWith("__")) {
            const completionItem = currentItems[item][COMPLETION_KEY];
            if (typeof completionItem !== 'undefined') {
              // Monaco adds a 'range' to the object, to denote where the autocomplete is triggered from,
              // which needs to be removed each time the autocomplete objects are re-used
              delete completionItem.range;
              if ('documentation' in completionItem && typeof completionItem.documentation !== 'object') {
                let docs = completionItem.documentation;
                completionItem.documentation = {
                  value: docs,
                  isTrusted: true,
                  supportsHtml: true
                }
              }
              // Add to final results
              result.push(completionItem);
            }
          }
        }
      }

      return {
        suggestions: result
      };
    }
  });
}

/**
 * Register hover items with the Monaco editor, for the Twig language
 *
 * @param completionItems array of completion items, with sub-properties in `COMPLETION_KEY`
 * @param autocompleteType the type of autocomplete, either `TwigExpressionAutocomplete` or `GeneralAutocomplete`
 * @param hasSubProperties where the autocomplete has sub-properties, and should be parsed as such
 */
function addHoverHandlerToMonaco(completionItems, autocompleteType, hasSubProperties) {
  monaco.languages.registerHoverProvider('twig', {
    provideHover: function (model, position) {
      let result = {};
      const currentLine = model.getValueInRange({
        startLineNumber: position.lineNumber,
        startColumn: 0,
        endLineNumber: position.lineNumber,
        endColumn: model.getLineMaxColumn(position.lineNumber)
      });
      const currentWord = model.getWordAtPosition(position);
      if (currentWord === null) {
        return;
      }
      let searchLine = currentLine.substring(0, currentWord.endColumn - 1)
      let isSubProperty = false;
      let currentItems = completionItems;
      for (let i = searchLine.length; i >= 0; i--) {
        if (searchLine[i] === ' ') {
          searchLine = currentLine.substring(i + 1, searchLine.length);
          break;
        }
      }
      if (searchLine.includes('.')) {
        isSubProperty = true;
      }
      if (isSubProperty) {
        // Is a sub-property, get a list of parent properties
        const parents = searchLine.substring(0, searchLine.length).split(".");
        // Loop through all the parents to traverse the completion items and find the current one
        for (let i = 0; i < parents.length - 1; i++) {
          const thisParent = parents[i].replace(/[{(<]/, '');
          if (currentItems.hasOwnProperty(thisParent)) {
            currentItems = currentItems[thisParent];
          } else {
            return result;
          }
        }
      }
      if (typeof currentItems !== 'undefined' && typeof currentItems[currentWord.word] !== 'undefined') {
        const completionItem = currentItems[currentWord.word][COMPLETION_KEY];
        if (typeof completionItem !== 'undefined') {
          let docs = completionItem.documentation;
          if (typeof completionItem.documentation === 'object') {
            docs = completionItem.documentation.value;
          }
          return {
            range: new monaco.Range(position.lineNumber, currentWord.startColumn, position.lineNumber, currentWord.endColum),
            contents: [
              {value: '**' + completionItem.detail + '**'},
              {value: docs},
            ]
          }
        }
      }

      return result;
    }
  });
}

/**
 * Fetch the autocompletion items from local storage, or from the endpoint if they aren't cached in local storage
 */
function getCompletionItemsFromEndpoint(fieldType, endpointUrl) {
  let urlParams = '';
  if (typeof fieldType !== 'undefined' && fieldType !== null) {
    urlParams = '?fieldType=' + fieldType;
  }
  // Ping the controller endpoint
  let request = new XMLHttpRequest();
  request.open('GET', endpointUrl + urlParams, true);
  request.onload = function () {
    if (request.status >= 200 && request.status < 400) {
      const completionItems = JSON.parse(request.responseText);
      if (typeof window.monacoAutocompleteItems === 'undefined') {
        window.monacoAutocompleteItems = {};
      }
      // Don't add a completion more than once, as might happen with multiple Twigfield instances
      // on the same page, because the completions are global in Monaco
      for (const [name, autocomplete] of Object.entries(completionItems)) {
        if (!(autocomplete.name in window.monacoAutocompleteItems)) {
          window.monacoAutocompleteItems[autocomplete.name] = autocomplete.name;
          addCompletionItemsToMonaco(autocomplete.__completions, autocomplete.type, autocomplete.hasSubProperties);
          addHoverHandlerToMonaco(autocomplete.__completions, autocomplete.type);
        }
      }
    } else {
      console.log('Autocomplete endpoint failed with status ' + request.status)
    }
  };
  request.send();
}

export {getCompletionItemsFromEndpoint};
