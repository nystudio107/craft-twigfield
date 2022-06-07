<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\types;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
abstract class AutocompleteTypes
{
    // Constants
    // =========================================================================

    // Faux enum, No proper enums until PHP 8.1, and no constant visibility until PHP 7.1
    const TwigExpressionAutocomplete = 'TwigExpressionAutocomplete';
    const GeneralAutocomplete = 'GeneralAutocomplete';
}
