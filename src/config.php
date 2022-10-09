<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

/**
 * Twigfield config.php
 *
 * This file exists to store config settings for Twigfield. This file can
 * be used in place, or it can be put into @craft/config/ as `twigfield.php`
 *
 * This file is multi-environment aware as well, so you can have different
 * settings groups for each environment, just as you do for `general.php`
 */

use nystudio107\seomatic\autocompletes\SectionShorthandFieldsAutocomplete;
use nystudio107\twigfield\autocompletes\CraftApiAutocomplete;
use nystudio107\twigfield\autocompletes\TwigLanguageAutocomplete;

return [
    // Whether to allow anonymous access be allowed to the twigfield/autocomplete/index endpoint
    'allowFrontendAccess' => false,
    // The default autcompletes to use for the default `Twigfield` field type
    'defaultTwigfieldAutocompletes' => [
        CraftApiAutocomplete::class,
        TwigLanguageAutocomplete::class,
        SectionShorthandFieldsAutocomplete::class,
    ]
];
