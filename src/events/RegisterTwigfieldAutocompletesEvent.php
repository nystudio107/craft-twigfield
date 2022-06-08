<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\events;

use craft\events\RegisterComponentTypesEvent;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class RegisterTwigfieldAutocompletesEvent extends RegisterComponentTypesEvent
{
    /**
     * @var string The type of the field that the autocompletes should be generated for.
     */
    public $fieldType = '';
}
