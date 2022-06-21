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

use craft\web\twig\variables\Cp;
use nystudio107\twigfield\base\Autocomplete;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\types\AutocompleteTypes;
use nystudio107\twigfield\types\CompleteItemKind;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.0
 */
class EnvironmentVariableAutocomplete extends Autocomplete
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The name of the autocomplete
     */
    public $name = 'EnvironmentVariableAutocomplete';

    /**
     * @var string The type of the autocomplete
     */
    public $type = AutocompleteTypes::GeneralAutocomplete;

    /**
     * @var string Whether the autocomplete should be parsed with . -delimited nested sub-properties
     */
    public $hasSubProperties = false;

    // Public Methods
    // =========================================================================

    /**
     * Core function that generates the autocomplete array
     */
    public function generateCompleteItems(): void
    {
        $cp = new Cp();
        $suggestions = $cp->getEnvSuggestions(true);
        foreach ($suggestions as $suggestion) {
            foreach ($suggestion['data'] as $item) {
                $name = $item['name'];
                $prefix = $name[0];
                $trimmedName = ltrim($name, $prefix);
                CompleteItem::create()
                    ->label($name)
                    ->insertText($trimmedName)
                    ->filterText($trimmedName)
                    ->detail($item['hint'])
                    ->kind(CompleteItemKind::ConstantKind)
                    ->sortText('~' . $name)
                    ->add($this);
            }
        }
    }
}
