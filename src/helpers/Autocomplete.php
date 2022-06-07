<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\helpers;

use Craft;
use craft\events\RegisterComponentTypesEvent;
use nystudio107\twigfield\autocompletes\CraftApiAutocomplete;
use nystudio107\twigfield\base\Autocomplete as BaseAutoComplete;
use yii\base\Event;

/**
 * Class Autocomplete
 *
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class Autocomplete
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterComponentTypesEvent The event that is triggered when registering
     *        Twigfield Autocomplete types
     *
     * Autocomplete Generator types must implement [[AutocompleteInterface]]. [[AutoComplete]]
     * provides a base implementation.
     *
     * ```php
     * use nystudio107\twigfield\helpers\Autocomplete;
     * use craft\events\RegisterComponentTypesEvent;
     * use yii\base\Event;
     *
     * Event::on(Autocomplete::class,
     *     Autocomplete::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES,
     *     function(RegisterComponentTypesEvent $event) {
     *         $event->types[] = MyAutocomplete::class;
     *     }
     * );
     * ```
     */
    const EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES = 'registerAutocompleteGenerators';

    const DEFAULT_TWIGFIELD_AUTOCOMPLETES = [
        CraftApiAutocomplete::class,
    ];

    // Private Properties
    // =========================================================================

    private static $allAutocompletes;

    // Public Methods
    // =========================================================================

    /**
     * Call each of the autocompletes to generate their complete items
     */
    public static function generateAutocompletes(): array
    {
        $autocompleteItems = [];
        $autocompletes = self::getAllAutocompleteGenerators();
        foreach ($autocompletes as $autocomplete) {
            /* @var BaseAutoComplete $autocomplete */
            $autocomplete::generateCompleteItems();
            $autocompleteItems[] = [
                'name' => $autocomplete::getAutocompleteName(),
                'type' => $autocomplete::getAutocompleteType(),
                BaseAutoComplete::COMPLETION_KEY => $autocomplete::getCompleteItems()
            ];
        }
        Craft::info('Twigfield Autocompletes generated', __METHOD__);

        return array_merge([], ...$autocompleteItems);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns all available autocompletes classes.
     *
     * @return string[] The available autocompletes classes
     */
    public static function getAllAutocompleteGenerators(): array
    {
        if (self::$allAutocompletes) {
            return self::$allAutocompletes;
        }

        $event = new RegisterComponentTypesEvent([
            'types' => self::DEFAULT_TWIGFIELD_AUTOCOMPLETES
        ]);
        Event::trigger(self::class, self::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES, $event);
        self::$allAutocompletes = $event->types;

        return self::$allAutocompletes;
    }
}
