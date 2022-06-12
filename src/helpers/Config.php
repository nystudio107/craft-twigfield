<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\helpers;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\StringHelper;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class Config
{
    // Constants
    // =========================================================================

    const PHP_SUFFIX = '.php';

    // Static Methods
    // =========================================================================

    /**
     * Loads a config file from, trying @craft/config first, then from @nystudio107/twigfield
     *
     * @param string $fileName
     *
     * @return array
     */
    public static function getConfigFromFile(string $fileName): array
    {
        $fileName .= self::PHP_SUFFIX;
        $currentEnv = Craft::$app->getConfig()->env;
        // Try craft/config first
        $path = Craft::getAlias('@config/' . $fileName, false);
        if ($path === false || !file_exists($path)) {
            // Now try our own internal config
            $path = Craft::getAlias('@nystudio107/twigfield/config.php', false);
            if ($path === false || !file_exists($path)) {
                return [];
            }
        }
        // Make sure we got a config file
        if (!is_array($config = @include $path)) {
            return [];
        }
        // If it's not a multi-environment config, return the whole thing
        if (!array_key_exists('*', $config)) {
            return $config;
        }
        // If no environment was specified, just look in the '*' array
        if ($currentEnv === null) {
            return $config['*'];
        }
        $mergedConfig = [];
        foreach ($config as $env => $envConfig) {
            if ($env === '*' || StringHelper::contains($currentEnv, $env)) {
                $mergedConfig = ArrayHelper::merge($mergedConfig, $envConfig);
            }
        }

        return $mergedConfig;
    }
}
