<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\autocompletes;

use Craft;
use craft\base\ElementInterface;
use craft\events\SectionEvent;
use craft\services\Sections;
use nystudio107\twigfield\base\Autocomplete;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\Twigfield;
use nystudio107\twigfield\types\AutocompleteTypes;
use nystudio107\twigfield\types\CompleteItemKind;
use yii\base\Event;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.13
 */
class SectionShorthandFieldsAutocomplete extends Autocomplete
{
    // Constants
    // =========================================================================

    public const OPTIONS_DATA_KEY = 'SectionShorthandFields';

    // Public Properties
    // =========================================================================

    /**
     * @var string The name of the autocomplete
     */
    public $name = 'SectionShorthandFieldsAutocomplete';

    /**
     * @var string The type of the autocomplete
     */
    public $type = AutocompleteTypes::TwigExpressionAutocomplete;

    /**
     * @var string Whether the autocomplete should be parsed with . -delimited nested sub-properties
     */
    public $hasSubProperties = false;

    /**
     * @var ?int The section id
     */
    public $sectionId = null;

    // Public Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public function init(): void
    {
        $this->sectionId = $this->twigfieldOptions[self::OPTIONS_DATA_KEY] ?? null;
        // Base CP templates directory
        Event::on(Sections::class, Sections::EVENT_AFTER_SAVE_SECTION, function (SectionEvent $e) {
            $config = [
                'fieldType' => $this->fieldType,
                'twigfieldOptions' => [
                    'singleLineEditor' => true,
                    self::OPTIONS_DATA_KEY => $e->section->id,
                ],
            ];
            $cache = Craft::$app->getCache();
            $cacheKey = Twigfield::getInstance()->autocomplete->getAutocompleteCacheKey($this, $config);
            $cache->delete($cacheKey);
        });
    }

    /**
     * Core function that generates the autocomplete array
     */
    public function generateCompleteItems(): void
    {
        if ($this->sectionId) {
            $sections = Craft::$app->getSections();
            $section = $sections->getSectionById($this->sectionId);
            if ($section) {
                $entryTypes = $section->getEntryTypes();
                foreach ($entryTypes as $entryType) {
                    // Add the native fields in
                    if ($entryType->elementType) {
                        $element = new $entryType->elementType;
                        /* @var ElementInterface $element */
                        $nativeFields = $element->attributeLabels();
                        foreach ($nativeFields as $key => $value) {
                            CompleteItem::create()
                                ->insertText($key)
                                ->label($key)
                                ->detail(Craft::t('twigfield', 'Field Shorthand'))
                                ->documentation($element::displayName() . ' ' . $value)
                                ->kind(CompleteItemKind::PropertyKind)
                                ->add($this);
                        }
                    }
                    // Add the custom fields in
                    $customFields = $entryType->getCustomFields();
                    foreach ($customFields as $customField) {
                        $name = $customField->handle;
                        $docs = $customField->instructions ?? '';
                        if ($name) {
                            CompleteItem::create()
                                ->insertText($name)
                                ->label($name)
                                ->detail(Craft::t('twigfield', 'Custom Field Shorthand'))
                                ->documentation($docs)
                                ->kind(CompleteItemKind::FieldKind)
                                ->add($this);
                        }
                    }
                }
            }
        }
    }
}
