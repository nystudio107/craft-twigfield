<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\services;

use Craft;
use craft\base\Component;
use craft\events\SectionEvent;
use craft\services\Fields;
use nystudio107\twigfield\autocompletes\SectionShorthandFieldsAutocomplete;
use nystudio107\twigfield\base\Autocomplete as BaseAutoComplete;
use nystudio107\twigfield\base\AutocompleteInterface;
use nystudio107\twigfield\events\RegisterTwigfieldAutocompletesEvent;
use nystudio107\twigfield\Twigfield;
use yii\base\Event;
use yii\caching\TagDependency;

/**
 * Class Autocomplete
 *
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class AutocompleteService extends Component
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterTwigfieldAutocompletesEvent The event that is triggered when registering
     *        Twigfield Autocomplete types
     *
     * Autocomplete Generator types must implement [[AutocompleteInterface]]. [[AutoComplete]]
     * provides a base implementation.
     *
     * ```php
     * use nystudio107\twigfield\services\AutocompleteService;
     * use nystudio107\twigfield\events\RegisterTwigfieldAutocompletesEvent;
     * use yii\base\Event;
     *
     * Event::on(AutocompleteService::class,
     *     AutocompleteService::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES,
     *     function(RegisterTwigfieldAutocompletesEvent $event) {
     *         $event->types[] = MyAutocomplete::class;
     *     }
     * );
     *
     * or to pass in a config array for the Autocomplete object:
     *
     * Event::on(AutocompleteService::class,
     *     AutocompleteService::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES,
     *     function(RegisterTwigfieldAutocompletesEvent $event) {
     *         $config = [
     *             'property' => value,
     *         ];
     *         $event->types[] = [MyAutocomplete::class => $config];
     *     }
     * );
     *
     * ```
     */
    const EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES = 'registerTwigfieldAutocompletes';

    const AUTOCOMPLETE_CACHE_TAG = 'TwigFieldAutocompleteTag';

    // Public Properties
    // =========================================================================

    /**
     * @var string Prefix for the cache key
     */
    public $cacheKeyPrefix = 'TwigFieldAutocomplete';

    /**
     * @var int Cache duration
     */
    public $cacheDuration = 3600;

    // Public Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public function init(): void
    {
        parent::init();
        // Short cacheDuration if we're in devMode
        if (Craft::$app->getConfig()->getGeneral()->devMode) {
            $this->cacheDuration = 1;
        }
        // Invalidate any SectionShorthandFieldsAutocomplete caches whenever any field layout is edited
        Event::on(Fields::class, Fields::EVENT_AFTER_SAVE_FIELD_LAYOUT, function (SectionEvent $e) {
            $this->clearAutocompleteCache(SectionShorthandFieldsAutocomplete::class);
        });
    }

    /**
     * Call each of the autocompletes to generate their complete items
     * @param string $fieldType
     * @param array $twigfieldOptions
     * @return array
     */
    public function generateAutocompletes(string $fieldType = Twigfield::DEFAULT_FIELD_TYPE, array $twigfieldOptions = []): array
    {
        $autocompleteItems = [];
        $autocompletes = $this->getAllAutocompleteGenerators($fieldType);
        foreach ($autocompletes as $autocompleteGenerator) {
            /* @var BaseAutoComplete $autocomplete */
            // Assume the generator is a class name string
            $config = [
                'fieldType' => $fieldType,
                'twigfieldOptions' => $twigfieldOptions,
            ];
            $autocompleteClass = $autocompleteGenerator;
            // If we're passed in an array instead, extract the class name and config from the key/value pair
            // in the form of [className => configArray]
            if (is_array($autocompleteGenerator)) {
                $autocompleteClass = array_key_first($autocompleteGenerator);
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $config = array_merge($config, $autocompleteGenerator[$autocompleteClass]);
            }
            $autocomplete = new $autocompleteClass($config);
            $name = $autocomplete->name;
            // Set up the cache parameters
            $cache = Craft::$app->getCache();
            $cacheKey = $this->getAutocompleteCacheKey($autocomplete, $config);
            $dependency = new TagDependency([
                'tags' => [
                    self::AUTOCOMPLETE_CACHE_TAG,
                    self::AUTOCOMPLETE_CACHE_TAG . $name,
                    self::AUTOCOMPLETE_CACHE_TAG . get_class($autocomplete),
                ],
            ]);
            // Get the autocompletes from the cache, or generate them if they aren't cached
            $autocompleteItems[$name] = $cache->getOrSet($cacheKey, static function () use ($name, $autocomplete) {
                $autocomplete->generateCompleteItems();
                return [
                    'name' => $name,
                    'type' => $autocomplete->type,
                    'hasSubProperties' => $autocomplete->hasSubProperties,
                    BaseAutoComplete::COMPLETION_KEY => $autocomplete->getCompleteItems(),
                ];
            }, $this->cacheDuration, $dependency);
        }
        Craft::info('Twigfield Autocompletes generated', __METHOD__);

        return $autocompleteItems;
    }

    /**
     * Clear the specified autocomplete cache (or all autocomplete caches if left empty)
     *
     * @param string $autocompleteName
     * @return void
     */
    public function clearAutocompleteCache(string $autocompleteName = ''): void
    {
        $cache = Craft::$app->getCache();
        TagDependency::invalidate($cache, self::AUTOCOMPLETE_CACHE_TAG . $autocompleteName);
        Craft::info('Twigfield caches invalidated', __METHOD__);
    }

    /**
     * Return the cache key to use for an Autocomplete's complete items
     *
     * @param AutocompleteInterface $autocomplete
     * @param array $config
     * @return string
     */
    public function getAutocompleteCacheKey(AutocompleteInterface $autocomplete, array $config): string
    {
        return $this->cacheKeyPrefix . $autocomplete->name . md5(serialize($config));
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns all available autocompletes classes.
     *
     * @return string[] The available autocompletes classes
     */
    public function getAllAutocompleteGenerators(string $fieldType = Twigfield::DEFAULT_FIELD_TYPE): array
    {
        $event = new RegisterTwigfieldAutocompletesEvent([
            'types' => Twigfield::$settings->defaultTwigfieldAutocompletes,
            'fieldType' => $fieldType,
        ]);
        $this->trigger(self::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES, $event);

        return $event->types;
    }
}
