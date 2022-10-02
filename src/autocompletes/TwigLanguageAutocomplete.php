<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\autocompletes;

use Craft;
use nystudio107\twigfield\base\Autocomplete;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\types\AutocompleteTypes;
use nystudio107\twigfield\types\CompleteItemKind;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.0
 */
class TwigLanguageAutocomplete extends Autocomplete
{
    // Constants
    // =========================================================================

    const CRAFT_FILTER_DOCS_URL = 'https://craftcms.com/docs/4.x/dev/filters.html';
    const FILTER_DOCS = [
        'abs' => '[abs](https://twig.symfony.com/doc/3.x/filters/abs.html) | Returns the absolute value of a number.',
        'address' => '[address](#address) | Formats an address.',
        'append' => '[append](#append) | Appends HTML to the end of another element.',
        'ascii' => '[ascii](#ascii) | Converts a string to ASCII characters.',
        'atom' => '[atom](#atom) | Converts a date to an ISO-8601 timestamp.',
        'attr' => '[attr](#attr) | Modifies an HTML tag’s attributes.',
        'batch' => '[batch](https://twig.symfony.com/doc/3.x/filters/batch.html) | Batches items in an array.',
        'camel' => '[camel](#camel) | Formats a string into camelCase.',
        'capitalize' => '[capitalize](https://twig.symfony.com/doc/3.x/filters/capitalize.html) | Capitalizes the first character of a string.',
        'column' => '[column](#column) | Returns the values from a single property or key in an array.',
        'contains' => '[contains](#contains) | Returns whether an array contains a nested item with a given key-value pair.',
        'convert_encoding' => '[convert_encoding](https://twig.symfony.com/doc/3.x/filters/convert_encoding.html) | Converts a string from one encoding to another.',
        'currency' => '[currency](#currency) | Formats a number as currency.',
        'date' => '[date](#date) | Formats a date.',
        'date_modify' => '[date_modify](https://twig.symfony.com/doc/3.x/filters/date_modify.html) | Modifies a date.',
        'datetime' => '[datetime](#datetime) | Formats a date with its time.',
        'default' => '[default](https://twig.symfony.com/doc/3.x/filters/default.html) | Returns the value or a default value if empty.',
        'diff' => '[diff](#diff) | Returns the difference between arrays.',
        'duration' => '[duration](#duration) | Returns a `DateInterval` object.',
        'e' => '[e](https://twig.symfony.com/doc/3.x/filters/escape.html) | Escapes a string.',
        'encenc' => '[encenc](#encenc) | Encrypts and base64-encodes a string.',
        'escape' => '[escape](https://twig.symfony.com/doc/3.x/filters/escape.html) | Escapes a string.',
        'explodeClass' => '[explodeClass](#explodeclass) | Converts a `class` attribute value into an array of class names.',
        'explodeStyle' => '[explodeStyle](#explodestyle) | Converts a `style` attribute value into an array of property name/value pairs.',
        'filesize' => '[filesize](#filesize) | Formats a number of bytes into something else.',
        'filter' => '[filter](#filter) | Filters the items in an array.',
        'first' => '[first](https://twig.symfony.com/doc/3.x/filters/first.html) | Returns the first character/item of a string/array.',
        'format' => '[format](https://twig.symfony.com/doc/3.x/filters/format.html) | Formats a string by replacing placeholders.',
        'group' => '[group](#group) | Groups items in an array.',
        'hash' => '[hash](#hash) | Prefixes a string with a keyed-hash message authentication code (HMAC).',
        'httpdate' => '[httpdate](#httpdate) | Converts a date to the HTTP format.',
        'id' => '[id](#id) | Normalizes an element ID into only alphanumeric characters, underscores, and dashes.',
        'index' => '[index](#index) | Indexes the items in an array.',
        'indexOf' => '[indexOf](#indexof) | Returns the index of a given value within an array, or the position of a passed-in string within another string.',
        'intersect' => '[intersect](#intersect) | Returns the intersecting items of two arrays.',
        'join' => '[join](https://twig.symfony.com/doc/3.x/filters/join.html) | Concatenates multiple strings into one.',
        'json_decode' => '[json_decode](#json_decode) | JSON-decodes a value.',
        'json_encode' => '[json_encode](#json_encode) | JSON-encodes a value.',
        'kebab' => '[kebab](#kebab) | Formats a string into “kebab-case”.',
        'keys' => '[keys](https://twig.symfony.com/doc/3.x/filters/keys.html) | Returns the keys of an array.',
        'last' => '[last](https://twig.symfony.com/doc/3.x/filters/last.html) | Returns the last character/item of a string/array.',
        'lcfirst' => '[lcfirst](#lcfirst) | Lowercases the first character of a string.',
        'length' => '[length](https://twig.symfony.com/doc/3.x/filters/length.html) | Returns the length of a string or array.',
        'literal' => '[literal](#literal) | Escapes an untrusted string for use with element query params.',
        'lower' => '[lower](https://twig.symfony.com/doc/3.x/filters/lower.html) | Lowercases a string.',
        'map' => '[map](https://twig.symfony.com/doc/3.x/filters/map.html) | Applies an arrow function to the items in an array.',
        'markdown' => '[markdown](#markdown-or-md) | Processes a string as Markdown.',
        'md' => '[md](#markdown-or-md) | Processes a string as Markdown.',
        'merge' => '[merge](#merge) | Merges an array with another one.',
        'money' => '[money](#money) | Outputs a value from a Money object.',
        'multisort' => '[multisort](#multisort) | Sorts an array by one or more keys within its sub-arrays.',
        'namespace' => '[namespace](#namespace) | Namespaces input names and other HTML attributes, as well as CSS selectors.',
        'namespaceAttributes' => '',
        'namespaceInputId' => '[namespaceInputId](#namespaceinputid) | Namespaces an element ID.',
        'namespaceInputName' => '[namespaceInputName](#namespaceinputname) | Namespaces an input name.',
        'nl2br' => '[nl2br](https://twig.symfony.com/doc/3.x/filters/nl2br.html) | Replaces newlines with `<br>` tags.',
        'ns' => '[ns](#namespace) | Namespaces input names and other HTML attributes, as well as CSS selectors.',
        'number' => '[number](#number) | Formats a number.',
        'number_format' => '[number_format](https://twig.symfony.com/doc/3.x/filters/number_format.html) | Formats numbers.',
        'parseAttr' => '',
        'parseRefs' => '[parseRefs](#parserefs) | Parses a string for reference tags.',
        'pascal' => '[pascal](#pascal) | Formats a string into “PascalCase”.',
        'percentage' => '[percentage](#percentage) | Formats a percentage.',
        'prepend' => '[prepend](#prepend) | Prepends HTML to the beginning of another element.',
        'purify' => '[purify](#purify) | Runs HTML code through HTML Purifier.',
        'push' => '[push](#push) | Appends one or more items onto the end of an array.',
        'raw' => '[raw](https://twig.symfony.com/doc/3.x/filters/raw.html) | Marks as value as safe for the current escaping strategy.',
        'reduce' => '[reduce](https://twig.symfony.com/doc/3.x/filters/reduce.html) | Iteratively reduces a sequence or mapping to a single value.',
        'removeClass' => '[removeClass](#removeclass) | Removes a class (or classes) from the given HTML tag.',
        'replace' => '[replace](#replace) | Replaces parts of a string with other things.',
        'reverse' => '[reverse](https://twig.symfony.com/doc/3.x/filters/reverse.html) | Reverses a string or array.',
        'round' => '[round](https://twig.symfony.com/doc/3.x/filters/round.html) | Rounds a number.',
        'rss' => '[rss](#rss) | Converts a date to RSS date format.',
        'slice' => '[slice](https://twig.symfony.com/doc/3.x/filters/slice.html) | Extracts a slice of a string or array.',
        'snake' => '[snake](#snake) | Formats a string into “snake_case”.',
        'sort' => '[sort](https://twig.symfony.com/doc/3.x/filters/sort.html) | Sorts an array.',
        'spaceless' => '[spaceless](https://twig.symfony.com/doc/3.x/filters/spaceless.html) | Removes whitespace between HTML tags.',
        'split' => '[split](https://twig.symfony.com/doc/3.x/filters/split.html) | Splits a string by a delimiter.',
        'striptags' => '[striptags](https://twig.symfony.com/doc/3.x/filters/striptags.html) | Strips SGML/XML tags from a string.',
        't' => '[t](#translate-or-t) | Translates a message.',
        'time' => '[time](#time) | Formats a time.',
        'timestamp' => '[timestamp](#timestamp) | Formats a human-readable timestamp.',
        'title' => '[title](https://twig.symfony.com/doc/3.x/filters/title.html) | Formats a string into “Title Case”.',
        'translate' => '[translate](#translate-or-t) | Translates a message.',
        'trim' => '[trim](https://twig.symfony.com/doc/3.x/filters/trim.html) | Strips whitespace from the beginning and end of a string.',
        'truncate' => '[truncate](#truncate) | Truncates a string to a given length, while ensuring that it does not split words.',
        'ucfirst' => '[ucfirst](#ucfirst) | Capitalizes the first character of a string.',
        'ucwords' => '',
        'unique' => '[unique](#unique) | Removes duplicate values from an array.',
        'unshift' => '[unshift](#unshift) | Prepends one or more items to the beginning of an array.',
        'upper' => '[upper](https://twig.symfony.com/doc/3.x/filters/upper.html) | Formats a string into “UPPER CASE”.',
        'url_encode' => '[url_encode](https://twig.symfony.com/doc/3.x/filters/url_encode.html) | Percent-encodes a string as a URL segment or an array as a query string.',
        'values' => '[values](#values) | Returns all the values in an array, resetting its keys.',
        'where' => '[where](#where) | Filters an array by key-value pairs.',
        'widont' => '',
        'without' => '[without](#without) | Returns an array without the specified element(s).',
        'withoutKey' => '[withoutKey](#withoutkey) | Returns an array without the specified key.',
    ];

    const CRAFT_FUNCTION_DOCS_URL = 'https://craftcms.com/docs/4.x/dev/functions.html';
    const FUNCTION_DOCS = [
        'actionInput' => '[actionInput](#actioninput) | Outputs a hidden `action` input.',
        'actionUrl' => '[actionUrl](#actionurl) | Generates a controller action URL.',
        'alias' => '[alias](#alias) | Parses a string as an alias.',
        'attr' => '[attr](#attr) | Generates HTML attributes.',
        'beginBody' => '[beginBody](#beginbody) | Outputs scripts and styles that were registered for the “begin body” position.',
        'ceil' => '[ceil](#ceil) | Rounds a number up.',
        'className' => '[className](#classname) | Returns the fully qualified class name of a given object.',
        'clone' => '[clone](#clone) | Clones an object.',
        'collect' => '[collect](#collect) | Returns a new collection.',
        'combine' => '[combine](#combine) | Combines two arrays into one.',
        'configure' => '[configure](#configure) | Sets attributes on the passed object.',
        'constant' => '[constant](https://twig.symfony.com/doc/3.x/functions/constant.html) | Returns the constant value for a given string.',
        'cpUrl' => '[cpUrl](#cpurl) | Generates a control panel URL.',
        'create' => '[create](#create) | Creates a new object.',
        'csrfInput' => '[csrfInput](#csrfinput) | Returns a hidden CSRF token input.',
        'cycle' => '[cycle](https://twig.symfony.com/doc/3.x/functions/cycle.html) | Cycles on an array of values.',
        'dataUrl' => '[dataUrl](#dataurl) | Outputs an asset or file as a base64-encoded data URL.',
        'date' => '[date](#date) | Creates a date.',
        'dump' => '[dump](https://twig.symfony.com/doc/3.x/functions/dump.html) | Dumps information about a variable.',
        'endBody' => '[endBody](#endbody) | Outputs scripts and styles that were registered for the “end body” position.',
        'expression' => '[expression](#expression) | Creates a database expression object.',
        'failMessageInput' => '[failMessageInput](#failmessageinput) | Outputs a hidden `failMessage` input.',
        'floor' => '[floor](#floor) | Rounds a number down.',
        'getenv' => '[getenv](#getenv) | Returns the value of an environment variable.',
        'gql' => '[gql](#gql) | Executes a GraphQL query against the full schema.',
        'head' => '[head](#head) | Outputs scripts and styles that were registered for the “head” position.',
        'hiddenInput' => '[hiddenInput](#hiddeninput) | Outputs a hidden input.',
        'include' => '[include](https://twig.symfony.com/doc/3.x/functions/include.html) | Returns the rendered content of a template.',
        'input' => '[input](#input) | Outputs an HTML input.',
        'max' => '[max](https://twig.symfony.com/doc/3.x/functions/max.html) | Returns the biggest value in an array.',
        'min' => '[min](https://twig.symfony.com/doc/3.x/functions/min.html) | Returns the lowest value in an array.',
        'ol' => '[ol](#ol) | Outputs an array of items as an ordered list.',
        'parseBooleanEnv' => '[parseBooleanEnv](#parsebooleanenv) | Parses a string as an environment variable or alias having a boolean value.',
        'parseEnv' => '[parseEnv](#parseenv) | Parses a string as an environment variable or alias.',
        'plugin' => '[plugin](#plugin) | Returns a plugin instance by its handle.',
        'random' => '[random](https://twig.symfony.com/doc/3.x/functions/random.html) | Returns a random value.',
        'range' => '[range](https://twig.symfony.com/doc/3.x/functions/range.html) | Returns a list containing an arithmetic progression of integers.',
        'raw' => '[raw](#raw) | Wraps the given string in a `Twig\Markup` object to prevent it from getting HTML-encoded when output.',
        'redirectInput' => '[redirectInput](#redirectinput) | Outputs a hidden `redirect` input.',
        'renderObjectTemplate' => '',
        'seq' => '[seq](#seq) | Outputs the next or current number in a sequence.',
        'shuffle' => '[shuffle](#shuffle) | Randomizes the order of the items in an array.',
        'siteUrl' => '[siteUrl](#siteurl) | Generates a front-end URL.',
        'source' => '[source](https://twig.symfony.com/doc/3.x/functions/source.html) | Returns the content of a template without rendering it.',
        'successMessageInput' => '[successMessageInput](#successmessageinput) | Outputs a hidden `successMessage` input.',
        'svg' => '[svg](#svg) | Outputs an SVG document.',
        'tag' => '[tag](#tag) | Outputs an HTML tag.',
        'template_from_string' => '[template_from_string](https://twig.symfony.com/doc/3.x/functions/template_from_string.html) | Loads a template from a string.',
        'ul' => '[ul](#ul) | Outputs an array of items as an unordered list.',
        'url' => '[url](#url) | Generates a URL.',
    ];

    const CRAFT_TAG_DOCS_URL = 'https://craftcms.com/docs/4.x/dev/tags.html';
    const TAG_DOCS = [
        'apply' => '[apply](https://twig.symfony.com/doc/3.x/tags/apply.html) | Applies Twig filters to the nested template code.',
        'autoescape' => '[autoescape](https://twig.symfony.com/doc/3.x/tags/autoescape.html) | Controls the escaping strategy for the nested template code.',
        'block' => '[block](https://twig.symfony.com/doc/3.x/tags/block.html) | Defines a template block.',
        'cache' => '[cache](#cache) | Caches a portion of your template.',
        'css' => '[css](#css) | Registers a `<style>` tag on the page.',
        'dd' => '[dd](#dd) | Dump and die.',
        'deprecated' => '[deprecated](https://twig.symfony.com/doc/3.x/tags/deprecated.html) | Triggers a PHP deprecation error.',
        'do' => '[do](https://twig.symfony.com/doc/3.x/tags/do.html) | Does.',
        'embed' => '[embed](https://twig.symfony.com/doc/3.x/tags/embed.html) | Embeds another template.',
        'exit' => '[exit](#exit) | Ends the request.',
        'extends' => '[extends](https://twig.symfony.com/doc/3.x/tags/extends.html) | Extends another template.',
        'flush' => '',
        'for' => '[for](https://twig.symfony.com/doc/3.x/tags/for.html) | Loops through an array.',
        'from' => '[from](https://twig.symfony.com/doc/3.x/tags/from.html) | Imports macros from a template.',
        'header' => '[header](#header) | Sets an HTTP header on the response.',
        'hook' => '[hook](#hook) | Invokes a template hook.',
        'html' => '[html](#html) | Registers arbitrary HTML code on the page.',
        'if' => '[if](https://twig.symfony.com/doc/3.x/tags/if.html) | Conditionally executes the nested template code.',
        'import' => '[import](https://twig.symfony.com/doc/3.x/tags/import.html) | Imports macros from a template.',
        'include' => '[include](https://twig.symfony.com/doc/3.x/tags/include.html) | Includes another template.',
        'js' => '[js](#js) | Registers a `<script>` tag on the page.',
        'macro' => '[macro](https://twig.symfony.com/doc/3.x/tags/macro.html) | Defines a macro.',
        'namespace' => '[namespace](#namespace) | Namespaces input names and other HTML attributes, as well as CSS selectors.',
        'nav' => '[nav](#nav) | Creates a hierarchical nav menu.',
        'paginate' => '[paginate](#paginate) | Paginates an element query.',
        'redirect' => '[redirect](#redirect) | Redirects the browser.',
        'requireAdmin' => '',
        'requireEdition' => '',
        'requireGuest' => '[requireGuest](#requireguest) | Requires that no user is logged-in.',
        'requireLogin' => '[requireLogin](#requirelogin) | Requires that a user is logged-in.',
        'requirePermission' => '[requirePermission](#requirepermission) | Requires that a user is logged-in with a given permission.',
        'script' => '[script](#script) | Renders an HTML script tag on the page.',
        'set' => '[set](https://twig.symfony.com/doc/3.x/tags/set.html) | Sets a variable.',
        'switch' => '[switch](#switch) | Switch the template output based on a give value.',
        'tag' => '[tag](#tag) | Renders an HTML tag on the page.',
        'use' => '[use](https://twig.symfony.com/doc/3.x/tags/use.html) | Inherits from another template horizontally.',
        'with' => '[with](https://twig.symfony.com/doc/3.x/tags/with.html) | Creates a nested template scope.',
    ];

    // Public Properties
    // =========================================================================

    /**
     * @var string The name of the autocomplete
     */
    public $name = 'TwigLanguageAutocomplete';

    /**
     * @var string The type of the autocomplete
     */
    public $type = AutocompleteTypes::TwigExpressionAutocomplete;

    /**
     * @var string Whether the autocomplete should be parsed with . -delimited nested sub-properties
     */
    public $hasSubProperties = false;

    // Public Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public function generateCompleteItems(): void
    {
        $twig = Craft::$app->getView()->getTwig();
        // Twig Filters
        $filters = array_keys($twig->getFilters());
        foreach ($filters as $filter) {
            $docs = self::FILTER_DOCS[$filter] ?? '';
            $docs = str_replace('(#', '(' . self::CRAFT_FILTER_DOCS_URL . '#', $docs);
            CompleteItem::create()
                ->label($filter)
                ->insertText($filter)
                ->detail(Craft::t('twigfield', 'Twig Filter'))
                ->documentation($docs)
                ->kind(CompleteItemKind::MethodKind)
                ->add($this);
        }
        // Twig Functions
        $functions = array_keys($twig->getFunctions());
        foreach ($functions as $function) {
            $functionLabel = $function . '()';
            $docs = self::FUNCTION_DOCS[$function] ?? '';
            $docs = str_replace('(#', '(' . self::CRAFT_FUNCTION_DOCS_URL . '#', $docs);
            CompleteItem::create()
                ->label($functionLabel)
                ->insertText($functionLabel)
                ->detail(Craft::t('twigfield', 'Twig Function'))
                ->documentation($docs)
                ->kind(CompleteItemKind::FunctionKind)
                ->add($this);
        }
        // Twig Tags
        $tags = array_keys($twig->getTokenParsers());
        foreach ($tags as $tag) {
            $docs = self::TAG_DOCS[$tag] ?? '';
            $docs = str_replace('(#', '(' . self::CRAFT_TAG_DOCS_URL . '#', $docs);
            CompleteItem::create()
                ->label($tag)
                ->insertText($tag)
                ->detail(Craft::t('twigfield', 'Twig Tag'))
                ->documentation($docs)
                ->kind(CompleteItemKind::FieldKind)
                ->add($this);
        }
    }
}
