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

    const FILTER_DOCS = [
        'abs' => 'Returns the absolute value of a number.',
        'address' => 'Formats an address.',
        'append' => 'Appends HTML to the end of another element.',
        'ascii' => 'Converts a string to ASCII characters.',
        'atom' => 'Converts a date to an ISO-8601 timestamp.',
        'attr' => 'Modifies an HTML tag’s attributes.',
        'batch' => 'Batches items in an array.',
        'camel' => 'Formats a string into camelCase.',
        'capitalize' => '',
        'column' => '',
        'contains' => '',
        'convert_encoding' => '',
        'currency' => '',
        'date' => '',
        'date_modify' => '',
        'datetime' => '',
        'default' => '',
        'diff' => '',
        'duration' => '',
        'e' => '',
        'encenc' => '',
        'escape' => '',
        'explodeClass' => '',
        'explodeStyle' => '',
        'filesize' => '',
        'filter' => '',
        'filterByValue' => '',
        'first' => '',
        'format' => '',
        'group' => '',
        'hash' => '',
        'httpdate' => '',
        'id' => '',
        'index' => '',
        'indexOf' => '',
        'intersect' => '',
        'join' => '',
        'json_decode' => '',
        'json_encode' => '',
        'kebab' => '',
        'keys' => '',
        'last' => '',
        'lcfirst' => '',
        'length' => '',
        'literal' => '',
        'lower' => '',
        'map' => '',
        'markdown' => '',
        'md' => '',
        'merge' => '',
        'money' => '',
        'multisort' => '',
        'namespace' => '',
        'namespaceAttributes' => '',
        'namespaceInputId' => '',
        'namespaceInputName' => '',
        'nl2br' => '',
        'ns' => '',
        'number' => '',
        'number_format' => '',
        'parseAttr' => '',
        'parseRefs' => '',
        'pascal' => '',
        'percentage' => '',
        'prepend' => '',
        'purify' => '',
        'push' => '',
        'raw' => '',
        'reduce' => '',
        'removeClass' => '',
        'replace' => '',
        'reverse' => '',
        'round' => '',
        'rss' => '',
        'slice' => '',
        'snake' => '',
        'sort' => '',
        'spaceless' => '',
        'split' => '',
        'striptags' => '',
        't' => '',
        'time' => '',
        'timestamp' => '',
        'title' => '',
        'translate' => '',
        'trim' => '',
        'truncate' => '',
        'ucfirst' => '',
        'ucwords' => '',
        'unique' => '',
        'unshift' => '',
        'upper' => '',
        'url_encode' => '',
        'values' => '',
        'where' => '',
        'widont' => '',
        'without' => '',
        'withoutKey' => '',
    ];

    const FUNCTION_DOCS = [
        'actionInput' => '',
        'actionUrl' => '',
        'alias' => '',
        'attr' => '',
        'beginBody' => '',
        'ceil' => '',
        'className' => '',
        'clone' => '',
        'collect' => '',
        'combine' => '',
        'configure' => '',
        'constant' => '',
        'cpUrl' => '',
        'create' => '',
        'csrfInput' => '',
        'cycle' => '',
        'dataUrl' => '',
        'date' => '',
        'dump' => '',
        'endBody' => '',
        'expression' => '',
        'failMessageInput' => '',
        'floor' => '',
        'getenv' => '',
        'gql' => '',
        'head' => '',
        'hiddenInput' => '',
        'include' => '',
        'input' => '',
        'max' => '',
        'min' => '',
        'ol' => '',
        'parseBooleanEnv' => '',
        'parseEnv' => '',
        'plugin' => '',
        'random' => '',
        'range' => '',
        'raw' => '',
        'redirectInput' => '',
        'renderObjectTemplate' => '',
        'seq' => '',
        'shuffle' => '',
        'siteUrl' => '',
        'source' => '',
        'successMessageInput' => '',
        'svg' => '',
        'tag' => '',
        'template_from_string' => '',
        'ul' => '',
        'url' => '',
    ];

    const TAG_DOCS = [
        'apply' => '',
        'autoescape' => '',
        'block' => '',
        'cache' => '',
        'css' => '',
        'dd' => '',
        'deprecated' => '',
        'do' => '',
        'embed' => '',
        'exit' => '',
        'extends' => '',
        'flush' => '',
        'for' => '',
        'from' => '',
        'header' => '',
        'hook' => '',
        'html' => '',
        'if' => '',
        'import' => '',
        'include' => '',
        'js' => '',
        'macro' => '',
        'namespace' => '',
        'nav' => '',
        'paginate' => '',
        'redirect' => '',
        'requireAdmin' => '',
        'requireEdition' => '',
        'requireGuest' => '',
        'requireLogin' => '',
        'requirePermission' => '',
        'script' => '',
        'set' => '',
        'switch' => '',
        'tag' => '',
        'use' => '',
        'with' => '',
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

    // Public Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete array
     */
    public function generateCompleteItems(): void
    {
        $twig = Craft::$app->getView()->getTwig();
        // Twig Filters
        $filters = array_keys($twig->getFilters());
        foreach ($filters as $filter) {
            CompleteItem::create()
                ->label($filter)
                ->insertText($filter)
                ->detail(Craft::t('twigfield', 'Twig Filter'))
                ->documentation(self::FILTER_DOCS[$filter] ?? '')
                ->kind(CompleteItemKind::MethodKind)
                ->add($this);
        }
        // Twig Functions
        $functions = array_keys($twig->getFunctions());
        foreach ($functions as $function) {
            $functionLabel = $function . '()';
            CompleteItem::create()
                ->label($functionLabel)
                ->insertText($functionLabel)
                ->detail(Craft::t('twigfield', 'Twig Function'))
                ->documentation(self::FUNCTION_DOCS[$function] ?? '')
                ->kind(CompleteItemKind::FunctionKind)
                ->add($this);
        }
        // Twig Tags
        $tags = array_keys($twig->getTokenParsers());
        foreach ($tags as $tag) {
            CompleteItem::create()
                ->label($tag)
                ->insertText($tag)
                ->detail(Craft::t('twigfield', 'Twig Tag'))
                ->documentation(self::TAG_DOCS[$function] ?? '')
                ->kind(CompleteItemKind::FieldKind)
                ->add($this);
        }
    }
}
