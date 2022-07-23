<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\UrlHelper;
use craft\i18n\PhpMessageSource;
use craft\web\Application as CraftWebApp;
use craft\web\View;
use nystudio107\twigfield\helpers\Config;
use nystudio107\twigfield\models\Settings;
use nystudio107\twigfield\services\AutocompleteService;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Module;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 *
 * @property AutocompleteService $autocomplete
 */
class Twigfield extends Module implements BootstrapInterface
{
    // Constants
    // =========================================================================

    const ID = 'twigfield';

    const DEFAULT_FIELD_TYPE = 'Twigfield';

    // Public Static Properties
    // =========================================================================

    /**
     * @var Settings The Twigfield config settings
     */
    public static $settings = null;

    // Public Static Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public static function setInstance($instance)
    {
        // Set module id
        $instance->id = self::ID;
        parent::setInstance($instance);
    }

    // Public Properties
    // =========================================================================

    /**
     * @inerhitdoc
     */
    public $id = self::ID;

    // Public Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public function bootstrap($app)
    {
        // Only bootstrap if this is a CraftWebApp
        if (!$app instanceof CraftWebApp) {
            return;
        }
        // Set the instance of this module class, so we can later access it with `Twigfield::getInstance()`
        static::setInstance($this);
        // Configure our module
        $this->configureModule();
        // Register our components
        $this->registerComponents();
        // Register our event handlers
        $this->registerEventHandlers();
        Craft::info('Twigfield module bootstrapped', __METHOD__);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Configure our module
     *
     * @return void
     */
    protected function configureModule(): void
    {
        // Set up our alias
        Craft::setAlias('@nystudio107/twigfield', $this->getBasePath());
        Craft::setAlias('@twigfieldEndpointUrl', UrlHelper::actionUrl('twigfield/autocomplete/index'));
        // Register our module
        Craft::$app->setModule($this->id, $this);
        // Translation category
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$this->id]) && !isset($i18n->translations[$this->id . '*'])) {
            $i18n->translations[$this->id] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@nystudio107/twigfield/translations',
                'forceTranslation' => true,
                'allowOverrides' => true,
            ];
        }
        // Set our settings
        $config = Config::getConfigFromFile($this->id);
        self::$settings = new Settings($config);
    }

    /**
     * Registers our components
     *
     * @return void
     */
    public function registerComponents(): void
    {
        $this->setComponents([
            'autocomplete' => AutocompleteService::class,
        ]);
    }

    /**
     * Registers our event handlers
     *
     * @return void
     */
    public function registerEventHandlers(): void
    {
        // Base CP templates directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });
        // Base Site templates directory
        if (self::$settings->allowFrontendAccess) {
            Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
                if (is_dir($baseDir = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates')) {
                    $e->roots[$this->id] = $baseDir;
                }
            });
        }
    }
}
