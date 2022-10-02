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
use craft\base\Element;
use nystudio107\twigfield\base\ObjectParserAutocomplete;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\types\AutocompleteTypes;
use nystudio107\twigfield\types\CompleteItemKind;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.12
 */
class CraftApiAutocomplete extends ObjectParserAutocomplete
{
    // Constants
    // =========================================================================

    const ELEMENT_ROUTE_EXCLUDES = [
        'matrixblock',
        'globalset'
    ];

    // Public Properties
    // =========================================================================

    /**
     * @var string The name of the autocomplete
     */
    public $name = 'CraftApiAutocomplete';

    /**
     * @var string The type of the autocomplete
     */
    public $type = AutocompleteTypes::TwigExpressionAutocomplete;

    /**
     * @var string Whether the autocomplete should be parsed with . -delimited nested sub-properties
     */
    public $hasSubProperties = true;

    /**
     * @var array A key-value array of the Twig global variables to parse. If left empty, it will
     * default to the current Twig context global variables
     */
    public $twigGlobals = [];

    /**
     * @var array A key-value array of the Element Route variables (the injected `entry`, etc.
     * variable). If left empty, it will default to the current Element Route variables
     */
    public $elementRouteGlobals = [];

    /**
     * @var array A key-value array of additional global variables to parse for completions
     */
    public $additionalGlobals = [];

    // Public Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public function init(): void
    {
        if (empty($this->twigGlobals)) {
            $this->twigGlobals = Craft::$app->view->getTwig()->getGlobals();
        }
        if (empty($this->elementRouteGlobals)) {
            $this->elementRouteGlobals = $this->getElementRouteGlobals();
        }
    }

    /**
     * @inerhitDoc
     */
    public function generateCompleteItems(): void
    {
        // Gather up all of the globals to parse
        $globals = array_merge(
            $this->twigGlobals,
            $this->elementRouteGlobals,
            $this->additionalGlobals,
            $this->overrideValues()
        );
        foreach ($globals as $key => $value) {
            if (!in_array($key, parent::EXCLUDED_PROPERTY_NAMES, true)) {
                $type = gettype($value);
                switch ($type) {
                    case 'object':
                        $this->parseObject($key, $value, 0);
                        break;

                    case 'array':
                    case 'boolean':
                    case 'double':
                    case 'integer':
                    case 'string':
                        $kind = CompleteItemKind::VariableKind;
                        $path = $key;
                        $normalizedKey = preg_replace("/[^A-Za-z]/", '', $key);
                        if (ctype_upper($normalizedKey)) {
                            $kind = CompleteItemKind::ConstantKind;
                        }
                        // If this is an array, JSON-encode the keys. In the future, we could recursively parse the array
                        // To allow for nested values
                        if (is_array($value)) {
                            $value = json_encode(array_keys($value));
                        }
                        CompleteItem::create()
                            ->detail((string)$value)
                            ->kind($kind)
                            ->label((string)$key)
                            ->insertText((string)$key)
                            ->add($this, $path);
                        break;
                }
            }
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * Add in the element types that could be injected as route variables
     *
     * @return array
     */
    protected function getElementRouteGlobals(): array
    {
        $routeVariables = [];
        $elementTypes = Craft::$app->elements->getAllElementTypes();
        foreach ($elementTypes as $elementType) {
            /* @var Element $elementType */
            $key = $elementType::refHandle();
            if (!empty($key) && !in_array($key, self::ELEMENT_ROUTE_EXCLUDES)) {
                $routeVariables[$key] = new $elementType();
            }
        }

        return $routeVariables;
    }

    /**
     * Override certain values that we always want hard-coded
     *
     * @return array
     */
    protected function overrideValues(): array
    {
        return [
            // Set the nonce to a blank string, as it changes on every request
            'nonce' => '',
        ];
    }
}
