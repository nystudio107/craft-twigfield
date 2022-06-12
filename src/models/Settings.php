<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\models;

use craft\base\Model;
use craft\validators\ArrayValidator;
use nystudio107\twigfield\autocompletes\CraftApiAutocomplete;
use nystudio107\twigfield\autocompletes\EnvironmentVariableAutocomplete;
use nystudio107\twigfield\autocompletes\TwigLanguageAutocomplete;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool Whether to allow anonymous access be allowed to the twigfield/autocompelte/index endpoint
     */
    public $allowFrontendAccess = false;

    /**
     * @var string[] The default autcompletes to use for the default `Twigfield` field type
     */
    public $defaultTwigfieldAutocompletes = [
        CraftApiAutocomplete::class,
        TwigLanguageAutocomplete::class,
        EnvironmentVariableAutocomplete::class,
    ];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            ['allowFrontendAccess', 'boolean'],
            ['defaultTwigfieldAutocompletes', ArrayValidator::class],
        ];
    }
}
