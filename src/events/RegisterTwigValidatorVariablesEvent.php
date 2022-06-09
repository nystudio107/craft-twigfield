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

use yii\base\Event;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class RegisterTwigValidatorVariablesEvent extends Event
{
    /**
     * @var array Variables to be passed down to the Twig context during Twig template validation
     */
    public $variables = [];
}
