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
        'date' => '',
        'date_modify' => '',
        'format' => '',
        'replace' => '',
        'number_format' => '',
        'abs' => '',
        'round' => '',
        'url_encode' => '',
        'json_encode' => '',
        'convert_encoding' => '',
        'title' => '',
        'capitalize' => '',
        'upper' => '',
        'lower' => '',
        'striptags' => '',
        'trim' => '',
        'nl2br' => '',
        'spaceless' => '',
        'join' => '',
        'split' => '',
        'sort' => '',
        'merge' => '',
        'batch' => '',
        'column' => '',
        'filter' => '',
        'map' => '',
        'reduce' => '',
        'reverse' => '',
        'length' => '',
        'slice' => '',
        'first' => '',
        'last' => '',
        'default' => '',
        'keys' => '',
        'escape' => '',
        'e' => '',
        'raw' => '',
        'address' => '',
        'append' => '',
        'ascii' => '',
        'atom' => '',
        'attr' => '',
        'camel' => '',
        'contains' => '',
        'currency' => '',
        'datetime' => '',
        'diff' => '',
        'duration' => '',
        'encenc' => '',
        'explodeClass' => '',
        'explodeStyle' => '',
        'filesize' => '',
        'filterByValue' => '',
        'group' => '',
        'hash' => '',
        'httpdate' => '',
        'id' => '',
        'index' => '',
        'indexOf' => '',
        'intersect' => '',
        'json_decode' => '',
        'kebab' => '',
        'lcfirst' => '',
        'literal' => '',
        'markdown' => '',
        'md' => '',
        'money' => '',
        'multisort' => '',
        'namespace' => '',
        'namespaceAttributes' => '',
        'ns' => '',
        'namespaceInputName' => '',
        'namespaceInputId' => '',
        'number' => '',
        'parseAttr' => '',
        'parseRefs' => '',
        'pascal' => '',
        'percentage' => '',
        'prepend' => '',
        'purify' => '',
        'push' => '',
        'removeClass' => '',
        'rss' => '',
        'snake' => '',
        'time' => '',
        'timestamp' => '',
        'translate' => '',
        'truncate' => '',
        't' => '',
        'ucfirst' => '',
        'ucwords' => '',
        'unique' => '',
        'unshift' => '',
        'values' => '',
        'where' => '',
        'widont' => '',
        'without' => '',
        'withoutKey' => '',
    ];

    const FUNCTION_DOCS = [
        'max' => '',
        'min' => '',
        'range' => '',
        'constant' => '',
        'cycle' => '',
        'random' => '',
        'date' => '',
        'include' => '',
        'source' => '',
        'template_from_string' => '',
        'actionUrl' => '',
        'alias' => '',
        'ceil' => '',
        'className' => '',
        'clone' => '',
        'collect' => '',
        'combine' => '',
        'configure' => '',
        'cpUrl' => '',
        'create' => '',
        'dataUrl' => '',
        'expression' => '',
        'floor' => '',
        'getenv' => '',
        'gql' => '',
        'parseEnv' => '',
        'parseBooleanEnv' => '',
        'plugin' => '',
        'raw' => '',
        'renderObjectTemplate' => '',
        'seq' => '',
        'shuffle' => '',
        'siteUrl' => '',
        'url' => '',
        'actionInput' => '',
        'attr' => '',
        'csrfInput' => '',
        'failMessageInput' => '',
        'hiddenInput' => '',
        'input' => '',
        'ol' => '',
        'redirectInput' => '',
        'successMessageInput' => '',
        'svg' => '',
        'tag' => '',
        'ul' => '',
        'head' => '',
        'beginBody' => '',
        'endBody' => '',
        'dump' => '',
        'sprig' => '',
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
        $filters = array_keys($twig->getFilters());
        foreach ($filters as $filter) {
            CompleteItem::create()
                ->label($filter)
                ->insertText($filter)
                ->detail(Craft::t('twigfield', 'Twig Filter'))
                ->documentation(self::FILTER_DOCS[$filter] ?? '')
                ->kind(CompleteItemKind::FieldKind)
                ->add($this);
        }
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
    }
}
