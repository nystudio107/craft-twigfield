<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\assetbundles\twigfield;

use craft\web\AssetBundle;
use craft\web\View;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class TwigfieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@nystudio107/twigfield/web/assets/dist';
        $this->depends = [
        ];
        $this->css = [
            'css/vendors.css',
            'css/styles.css',
        ];
        $this->js = [
            'js/runtime.js',
            'js/vendors.js',
            'js/codefield-editor.js'
        ];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view): void
    {
        parent::registerAssetFiles($view);

        if ($view instanceof View) {
            $view->registerTranslations('twigfield', [
                'Twig code is supported.'
            ]);
        }
    }
}
