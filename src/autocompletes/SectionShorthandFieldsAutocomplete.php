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
use craft\elements\Entry;
use nystudio107\twigfield\base\ObjectParserAutocomplete;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\types\AutocompleteTypes;
use nystudio107\twigfield\types\CompleteItemKind;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.13
 */
class SectionShorthandFieldsAutocomplete extends ObjectParserAutocomplete
{
    // Constants
    // =========================================================================

    public const OPTIONS_DATA_KEY = 'SectionShorthandFields';

    /**
     * @array properties that are defined only using the `@property` docblock annotation
     */
    public const MAGIC_GETTER_PROPERTIES = [
        Entry::class => [
            'typeId' => "the entry type’s ID",
            'authorId' => "the entry author’s ID",
            'type' => "the entry type",
            'section' => "the entry’s section",
            'author' => "the entry’s author",
        ]
    ];

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
    public $hasSubProperties = true;

    /**
     * @inheritdoc
     */
    public $parseBehaviors = false;

    /**
     * @inheritdoc
     */
    public $parseMethods = false;

    /**
     * @inheritdoc
     */
    public $customPropertySortPrefix = '';

    /**
     * @inheritdoc
     */
    public $propertySortPrefix = '';

    /**
     * @inheritdoc
     */
    public $methodSortPrefix = '';

    /**
     * @var ?int The section id. A sectionId of 0 denotes we don't know what this section is, so use
     * a generic `Entry` and don't generate any complete items for custom fields
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
    }

    /**
     * Core function that generates the autocomplete array
     */
    public function generateCompleteItems(): void
    {
        if ($this->sectionId !== null) {
            // A sectionId of 0 denotes we don't know what this section is, so use
            // a generic `Entry` and don't generate any complete items for custom fields
            if ($this->sectionId === 0) {
                $element = new Entry();
                $this->parseObject('', $element, 0);
                $this->addMagicGetterProperties($element);

                return;
            }
            // Find the entry types used in the passed in sectionId
            $sections = Craft::$app->getSections();
            $section = $sections->getSectionById($this->sectionId);
            if ($section) {
                $entryTypes = $section->getEntryTypes();
                foreach ($entryTypes as $entryType) {
                    // Add the native fields in
                    if ($entryType->elementType) {
                        $element = new $entryType->elementType;
                        /* @var ElementInterface $element */
                        $this->parseObject('', $element, 0);
                        $this->addMagicGetterProperties($element);
                    }
                    // Add the custom fields in
                    if (method_exists($entryType, 'getCustomFields')) {
                        $customFields = $entryType->getCustomFields();
                    } else {
                        $customFields = $entryType->getFields();
                    }
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

    /**
     * Add in magic getter properties that are defined only in the `@property` docblock annotation
     *
     * @param ElementInterface $element
     * @return void
     */
    protected function addMagicGetterProperties(ElementInterface $element): void
    {
        foreach (self::MAGIC_GETTER_PROPERTIES as $key => $value) {
            if ($key === $element::class) {
                foreach ($value as $name => $docs) {
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
