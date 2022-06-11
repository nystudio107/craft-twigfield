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
     * @var mixed The object that should be passed into `renderObjectTemplate()` during the template rendering
     */
    public $object = null;

    /**
     * @var array Variables in key => value format that should be available during the template rendering
     */
    public $variables = [];
}
