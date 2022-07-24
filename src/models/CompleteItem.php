<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\models;

use craft\base\Model;
use craft\validators\ArrayValidator;
use nystudio107\twigfield\base\AutocompleteInterface;
use nystudio107\twigfield\types\CompleteItemKind;

/**
 * Based on: https://microsoft.github.io/monaco-editor/api/interfaces/monaco.languages.CompletionItem.html
 *
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.11
 */
class CompleteItem extends Model
{
    // Public Static Methods
    // =========================================================================

    /**
     * Factory method for complete item objects
     *
     * @return CompleteItem
     */
    public static function create(): CompleteItem
    {
        return new CompleteItem();
    }

    // Public Properties
    // =========================================================================

    /**
     * @var array An optional array of additional text edits that are applied when selecting this completion.
     * Edits must not overlap with the main edit nor with themselves.
     * ref: https://microsoft.github.io/monaco-editor/api/interfaces/monaco.editor.ISingleEditOperation.html
     */
    public $additionalTextEdits;

    /**
     * @var array A command that should be run upon acceptance of this item.
     * ref: https://microsoft.github.io/monaco-editor/api/interfaces/monaco.languages.Command.html
     */
    public $command;

    /**
     * @var array An optional set of characters that when pressed while this completion is active will accept
     * it first and then type that character. Note that all commit characters should have `length=1` and that
     * superfluous characters will be ignored.
     */
    public $commitCharacters;

    /**
     * @var string A human-readable string with additional information about this item, like type or symbol information.
     */
    public $detail;

    /**
     * @var string A human-readable string that represents a doc-comment.
     * Can contain Markdown
     */
    public $documentation;

    /**
     * @var string A string that should be used when filtering a set of completion items.
     * When falsy the `label` is used.
     */
    public $filterText;

    /**
     * @var string A string or snippet that should be inserted in a document when selecting this completion.
     */
    public $insertText = '';

    /**
     * @var int Additional rules (as bitmask) that should be applied when inserting this completion.
     */
    public $insertTextRules;

    /**
     * @var int The kind of this completion item. Based on the kind an icon is chosen by the editor.
     */
    public $kind = CompleteItemKind::PropertyKind;

    /**
     * @var string The label of this completion item. By default this is also the text that is inserted
     * when selecting this completion.
     */
    public $label;

    /**
     * @var bool Select this item when showing. Note that only one completion item can be selected and that
     * the editor decides which item that is. The rule is that the first item of those that match best is selected.
     */
    public $preselect;

    /**
     * @var array A range of text that should be replaced by this completion item.
     */
    public $range;

    /**
     * @var string A string that should be used when comparing this item with other items. When falsy
     * the `label` is used.
     */
    public $sortText;

    /**
     * @var array A modifier to the kind which affect how the item is rendered, e.g. Deprecated is rendered
     * with a strikeout
     */
    public $tags;

    // Public Methods
    // =========================================================================

    /**
     * An optional array of additional text edits that are applied when selecting this completion.
     * Edits must not overlap with the main edit nor with themselves.
     * ref: https://microsoft.github.io/monaco-editor/api/interfaces/monaco.editor.ISingleEditOperation.html
     * @param $value array
     */
    public function additionalTextEdits($value): self
    {
        $this->additionalTextEdits = $value;
        return $this;
    }

    /**
     * A command that should be run upon acceptance of this item.
     * ref: https://microsoft.github.io/monaco-editor/api/interfaces/monaco.languages.Command.html
     * @param $value array
     */
    public function command($value): self
    {
        $this->command = $value;
        return $this;
    }

    /**
     * An optional set of characters that when pressed while this completion is active will accept
     * it first and then type that character. Note that all commit characters should have `length=1` and that
     * superfluous characters will be ignored.
     * @param $value array
     */
    public function commitCharacters($value): self
    {
        $this->commitCharacters = $value;
        return $this;
    }

    /**
     * A human-readable string with additional information about this item, like type or symbol information.
     * @param $value string
     */
    public function detail($value): self
    {
        $this->detail = $value;
        return $this;
    }

    /**
     * A human-readable string that represents a doc-comment.
     * Can contain Markdown
     * @param $value string
     */
    public function documentation($value): self
    {
        $this->documentation = $value;
        return $this;
    }

    /**
     * A string that should be used when filtering a set of completion items.
     * When falsy the `label` is used.
     * @param $value string
     */
    public function filterText($value): self
    {
        $this->filterText = $value;
        return $this;
    }

    /**
     * A string or snippet that should be inserted in a document when selecting this completion.
     * @param $value string
     */
    public function insertText($value): self
    {
        $this->insertText = $value;
        return $this;
    }

    /**
     * Additional rules (as bitmask) that should be applied when inserting this completion.
     * @param $value int
     */
    public function insertTextRules($value): self
    {
        $this->insertTextRules = $value;
        return $this;
    }

    /**
     * The kind of this completion item. Based on the kind an icon is chosen by the editor.
     * @param $value int
     */
    public function kind($value): self
    {
        $this->kind = $value;
        return $this;
    }

    /**
     * The label of this completion item. By default this is also the text that is inserted
     * when selecting this completion.
     * @param $value string
     */
    public function label($value): self
    {
        $this->label = $value;
        return $this;
    }

    /**
     * Select this item when showing. Note that only one completion item can be selected and that
     * the editor decides which item that is. The rule is that the first item of those that match best is selected.
     * @param $value bool
     */
    public function preselect($value): self
    {
        $this->preselect = $value;
        return $this;
    }

    /**
     * A range of text that should be replaced by this completion item.
     * @param $value array
     */
    public function range($value): self
    {
        $this->range = $value;
        return $this;
    }

    /**
     * A string that should be used when comparing this item with other items. When falsy
     * the `label` is used.
     * @param $value string
     */
    public function sortText($value): self
    {
        $this->sortText = $value;
        return $this;
    }

    /**
     * A modifier to the kind which affect how the item is rendered, e.g. Deprecated is rendered
     * with a strikeout
     * @param $value array
     */
    public function tags($value): self
    {
        $this->tags = $value;
        return $this;
    }

    /**
     * Add the completion item to the passed in AutocompleteInterface static class
     *
     * @param AutocompleteInterface $autocomplete
     * @param string $path The . delimited path in the autocomplete array to the item; if omitted, will be set to the $item->label
     * @return void
     */
    public function add($autocomplete, string $path = ''): void
    {
        $autocomplete->addCompleteItem($this, $path);
    }

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [

            [
                [
                    'detail',
                    'documentation',
                    'filterText',
                    'insertText',
                    'label',
                    'sortText',
                    'tags',
                ],
                'string'
            ],
            [
                [
                    'additionalTextEdits',
                    'command',
                    'commitCharacters',
                    'range',
                ],
                ArrayValidator::class
            ],
            ['insertTextRules', 'integer', 'min' => 0, 'max' => 4],
            ['kind', 'integer', 'min' => 0, 'max' => 27],
            ['preselect', 'boolean'],
            [
                [
                    'insertText',
                    'kind',
                ],
                'required'
            ],
        ];
    }
}
