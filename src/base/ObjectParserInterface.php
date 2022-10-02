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

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.12
 */
interface ObjectParserInterface
{
    // Public Methods
    // =========================================================================

    /**
     * Parse the object passed in, including any properties or methods
     *
     * @param string $name
     * @param $object
     * @param int $recursionDepth
     * @param string $path
     */
    public function parseObject(string $name, $object, int $recursionDepth, string $path = ''): void;
}
