<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\behaviors;

use Craft;
use yii\base\Behavior;

/**
 * @author    nystudio107.com
 * @package   Twigfield
 * @since     1.0.0
 */
class TwigfieldBehavior extends Behavior
{
    // Public Methods
    // =========================================================================

    /**
     * Return the twigfieldBaseAssetsUrl
     * @return false|string
     */
    public function getTwigfieldBaseAssetsUrl(): string
    {
        return Craft::$app->assetManager->getPublishedUrl(
            '@nystudio107/twigfield/web/assets/dist',
            true
        );
    }
}
