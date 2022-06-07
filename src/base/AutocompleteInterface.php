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

use nystudio107\twigfield\models\CompleteItem;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.0
 */
interface AutocompleteInterface
{
    // Constants
    // =========================================================================

    // Public Static Methods
    // =========================================================================

    /**
     * Generate the complete items
     */
    public static function generateCompleteItems(): void;

    /**
     * Add a complete item to the $path, which is a . separated namespace for the item
     * that indicates where in the associative array the item should appear.
     *
     * @param CompleteItem $item
     * @param string $path
     */
    public static function addCompleteItem(CompleteItem $item, string $path = ''): void;

    /**
     * Get the array of complete items
     *
     * @return array
     */
    public static function getCompleteItems(): array;
}
