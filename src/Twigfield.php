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
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\Application as CraftWebApp;
use craft\web\View;
use yii\base\Application as YiiApp;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Module;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class Twigfield extends Module implements BootstrapInterface
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterComponentTypesEvent The event that is triggered when registering
     *        Autocomplete Generator types
     *
     * Autocomplete Generator types must implement [[GeneratorInterface]]. [[Generator]]
     * provides a base implementation.
     *
     * ```php
     * use nystudio107\autocomplete\Autocomplete;
     * use craft\events\RegisterComponentTypesEvent;
     * use yii\base\Event;
     *
     * Event::on(Autocomplete::class,
     *     Autocomplete::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS,
     *     function(RegisterComponentTypesEvent $event) {
     *         $event->types[] = MyAutocompleteGenerator::class;
     *     }
     * );
     * ```
     */
    const EVENT_REGISTER_AUTOCOMPLETE_GENERATORS = 'registerAutocompleteGenerators';

    const DEFAULT_AUTOCOMPLETE_GENERATORS = [
        AutocompleteVariableGenerator::class,
        AutocompleteTwigExtensionGenerator::class,
    ];

    // Public Properties
    // =========================================================================

    public $id = 'twigfield';

    // Public Methods
    // =========================================================================

    /**
     * Bootstraps the extension
     *
     * @param YiiApp $app
     */
    public function bootstrap($app)
    {
        // Set the currently requested instance of this module class,
        // so we can later access it with `Twigfield::getInstance()`
        static::setInstance($this);
        // Make sure it's Craft
        if (!$app instanceof CraftWebApp) {
            return;
        }
        // Set up our alias
        Craft::setAlias('@nystudio107/twigfield', $this->getBasePath());
        // Register our module
        Craft::$app->setModule($this->id, $this);
        // Register our event handlers
        $this->registerEventHandlers();
    }

    /**
     * Registers our event handlers
     */
    public function registerEventHandlers()
    {
        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });
        Craft::info('Event Handlers installed', __METHOD__);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns all available autocomplete generator classes.
     *
     * @return string[] The available autocomplete generator classes
     */
    public function getAllAutocompleteGenerators(): array
    {
        if ($this->allAutocompleteGenerators) {
            return $this->allAutocompleteGenerators;
        }

        $event = new RegisterComponentTypesEvent([
            'types' => self::DEFAULT_AUTOCOMPLETE_GENERATORS
        ]);
        $this->trigger(self::EVENT_REGISTER_AUTOCOMPLETE_GENERATORS, $event);
        $this->allAutocompleteGenerators = $event->types;

        return $this->allAutocompleteGenerators;
    }
}
