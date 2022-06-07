<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\autocomplete\events;

use yii\base\Event;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class RegisterAutocompletesEvent extends Event
{
    /**
     * @var array Key-value pairs of values that will be used by the generator.
     */
    public $autocompletes = [];
}
