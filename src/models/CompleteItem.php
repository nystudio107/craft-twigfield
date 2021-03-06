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

use craft\validators\ArrayValidator;
use nystudio107\twigfield\base\AutocompleteInterface;
use nystudio107\twigfield\base\FluentModel;
use nystudio107\twigfield\types\CompleteItemKind;

/**
 * Based on: https://microsoft.github.io/monaco-editor/api/interfaces/monaco.languages.CompletionItem.html
 *
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 *
 * @method CompleteItem additionalTextEdits(string $additionalTextEdits) An optional array of additional text edits that are applied when selecting this completion. Edits must not overlap with the main edit nor with themselves.
 * @method CompleteItem command(array $command) A command that should be run upon acceptance of this item.
 * @method CompleteItem commitCharacters(array $commitCharacters) An optional set of characters that when pressed while this completion is active will accept it first and then type that character. Note that all commit characters should have `length=1` and that superfluous characters will be ignored.
 * @method CompleteItem detail(string $detail) A human-readable string with additional information about this item, like type or symbol information.
 * @method CompleteItem documentation(string $documentation) A human-readable string that represents a doc-comment.
 * Can contain Markdown
 * @method CompleteItem filterText(string $filterText) A string that should be used when filtering a set of completion items. When falsy the `label` is used.
 * @method CompleteItem insertText(string $insertText) A string or snippet that should be inserted in a document when selecting this completion.
 * @method CompleteItem insertTextRules(int $insertTextRules) Additional rules (as bitmask) that should be applied when inserting this completion.
 * @method CompleteItem kind(int $kind) The kind of this completion item. Based on the kind an icon is chosen by the editor.
 * @method CompleteItem label(string $label) The label of this completion item. By default this is also the text that is inserted when selecting this completion.
 * @method CompleteItem preselect(bool $preselect) Select this item when showing. Note that only one completion item can be selected and that the editor decides which item that is. The rule is that the first item that matches best is selected.
 * @method CompleteItem range(array $range) A range of text that should be replaced by this completion item.
 * @method CompleteItem sortText(string $sortText) A string that should be used when comparing this item with other items. When falsy the `label` is used.
 * @method CompleteItem tags(array $tags) A modifier to the kind which affect how the item is rendered, e.g. Deprecated is rendered with a strikeout
 */
class CompleteItem extends FluentModel
{
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

    // Public Methods
    // =========================================================================

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
