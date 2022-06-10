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
use craft\base\Model;
use yii\base\InvalidArgumentException;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
abstract class FluentModel extends Model
{

    // Public Methods
    // =========================================================================

    /**
     * Add fluent getters/setters for this class
     *
     * @param string $method The method name (property name)
     * @param array $args The arguments list
     *
     * @return mixed The value of the property
     */
    public function __call($method, $args)
    {
        try {
            $reflector = new \ReflectionClass(static::class);
        } catch (\ReflectionException $e) {
            Craft::error(
                $e->getMessage(),
                __METHOD__
            );

            return null;
        }
        if (!$reflector->hasProperty($method)) {
            throw new InvalidArgumentException("Property {$method} doesn't exist");
        }
        $property = $reflector->getProperty($method);
        if (empty($args)) {
            // Return the property
            return $property->getValue();
        }
        // Set the property
        $value = $args[0];
        $property->setValue($this, $value);

        // Make it chainable
        return $this;
    }
}
