<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\base;

use Craft;
use craft\helpers\ArrayHelper;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\types\AutocompleteTypes;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.0
 */
abstract class Autocomplete implements AutocompleteInterface
{
    // Constants
    // =========================================================================

    const COMPLETION_KEY = '__completions';

    // Protected Static Properties
    // =========================================================================

    /**
     * @var array The accumulated complete items
     */
    protected static $completeItems = [];

    // Public Static Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public static function getAutocompleteName(): string
    {
        return 'BaseAutocomplete';
    }

    /**
     * @inerhitDoc
     */
    public static function getAutocompleteType(): string
    {
        return AutocompleteTypes::TwigExpressionAutocomplete;
    }

    /**
     * @inerhitDoc
     */
    public static function generateCompleteItems(): void
    {
    }

    /**
     * @inerhitDoc
     */
    public static function addCompleteItem(CompleteItem $item, string $path = ''): void
    {
        if (!$item->validate()) {
            Craft::debug(print_r($item->getErrors(), true), __METHOD__);
            return;
        }
        ArrayHelper::setValue(static::$completeItems, $path, [self::COMPLETION_KEY => $item->toArray()]);
    }

    /**
     * @inerhitDoc
     */
    public static function getCompleteItems(): array
    {
        return static::$completeItems;
    }
}
