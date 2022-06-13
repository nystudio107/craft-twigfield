[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nystudio107/craft-twigfield/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/nystudio107/craft-twigfield/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/nystudio107/craft-twigfield/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/nystudio107/craft-twigfield/?branch=develop) [![Build Status](https://scrutinizer-ci.com/g/nystudio107/craft-twigfield/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/nystudio107/craft-twigfield/build-status/develop) [![Code Intelligence Status](https://scrutinizer-ci.com/g/nystudio107/craft-twigfield/badges/code-intelligence.svg?b=develop)](https://scrutinizer-ci.com/code-intelligence)

# Twigfield for Craft CMS 3.x & 4.x

Provides a twig editor field with Twig & Craft API autocomplete

## Requirements

Twigfield requires Craft CMS 3.0 or 4.0.

## Installation

To install Twigfield, follow these steps:

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to require the package:

        composer require nystudio107/craft-twigfield

## About Twigfield

Twigfield provides a full-featured Twig editor with syntax highlighting via the powerful [Monaco Editor](https://microsoft.github.io/monaco-editor/) (the same editor that is the basis for VS Code).

Twigfield provides full autocompletion for [Twig](https://twig.symfony.com/doc/3.x/) filters/functions/tags, and the full [Craft CMS](https://craftcms.com/docs/4.x/) API, including installed plugins.

You can also add your own custom Autocompletes, and customize the look and operation of the editor.

Twigfield also provides a [Yii2 Validator](https://www.yiiframework.com/doc/guide/2.0/en/tutorial-core-validators) for Twig templates and object templates.

## Using Twigfield

Once you've added the `nystudio107/craft-twigfield` package to your plugin, module, or project, no further setup is needed. This is because it operates as an auto-bootstrapping Yii2 Module.

Twigfield is not a Craft CMS plugin, rather a package to be utilized by a plugin, module, or project.

### In the Craft CP

Twigfield works just like the Craft CMS `forms` macros that should be familiar to plugin and module developers.

Simply import the macros:

```twig
{% import "twigfield/twigfield" as twigfield %}
```

Then to create a `textarea` do the following:

```twig
{{ twigfield.textarea( {
    id: 'myTwigfield',
    name: 'myTwigfield',
    value: textAreaText,
} }}
```

...where `textAreaText` is a variable containing the initial text that should be in the editor field. This will create the Twig editor.

To create a `textareaField` do the following:

```twig
{{ twigfield.textareaField( {
    label: "Twig Editor"|t,
    instructions: "Enter any Twig code below, with full API autocompletion."|t,
    id: 'myTwigfield',
    name: 'myTwigfield',
    value: textAreaText,
} }}
```

...where `textAreaText` is a variable containing the initial text that should be in the editor field. This will create the `label` and `instructions`, along with the Twig editor.

In either case, an Asset Bundle containing the necessary CSS & JavaScript for the editor to function will be included, and the editor initialized.

### In Frontend Templates

By default, Twigfield will not work in frontend templates, unless you specifically enable it.

Do so by copying the `config.php` file to the Craft CMS `config/` directory, renaming the file to `twigfield.php` in the process, then set the `allowFrontendAccess` setting to `true`:

```php
return [
    // Whether to allow anonymous access be allowed to the twigfield/autocompelte/index endpoint
    'allowFrontendAccess' => true,
    // The default autcompletes to use for the default `Twigfield` field type
    'defaultTwigfieldAutocompletes' => [
        CraftApiAutocomplete::class,
        TwigLanguageAutocomplete::class,
    ]
];
```

Then import the macros:

```twig
{% import "twigfield/twigfield" as twigfield %}
```

Create your own `<textarea>` element and include the necessary JavaScript, passing in the `id` of your `textarea` element:

```html
<textarea id="myTwigfield">
</textarea>
{{ twigfield.includeJs("myTwigfield") }}
```

Enabling the `allowFrontendAccess` setting allows access to the `twigfield/autocomplete/index` endpoint, and add the `twigfield/templates` directory to the template roots.

### Additional Options

The `textarea`, `textareaField`, and `includeJs` macros all take three additional optional parameters:

```twig
{{ textarea(config, fieldType, wrapperClass, options) }}

{{ textareaField(config, fieldType, wrapperClass, options }}

{{ includeJs(fieldId, fieldType, wrapperClass, options }}
```

#### `fieldType`

**`fieldType`** - by default this is set to `Twigfield`. You only need to change it to something else if you're using a custom Autocomplete (see below)

e.g.:

```twig
{{ twigfield.textarea({
    id: 'myTwigfield',
    name: 'myTwigfield',
    value: textAreaText,
}, "MyCustomFieldType" }}
```

#### `wrapperClass`

**`wrapperClass`** - an additional class that is added to the Twigfield editor wrapper `div`. By default, this is an empty string.

e.g.:

```twig
{{ twigfield.textareaField({
    label: "Twig Editor"|t,
    instructions: "Enter any Twig code below, with full API autocompletion."|t,
    id: 'myTwigfield',
    name: 'myTwigfield',
    value: textAreaText,
}, "Twigfield", "monaco-editor-background-frame" }}
```

The `monaco-editor-background-frame` class is bundled, and will cause the field to look like a Craft CMS editor field, but you can use your own class as well.

#### `options`

**`options`** - an optional [EditorOption](https://microsoft.github.io/monaco-editor/api/enums/monaco.editor.EditorOption.html) passed in to configure the Monaco editor. By default, this is an empty object.

e.g.:

```html
<textarea id="myTwigfield">
</textarea>
{{ twigfield.includeJs("myTwigfield", "Twigfield", "monaco-editor-background-frame", {
    lineNumbers: 'on',
}) }}
```

## Using Additional Autocompletes

By default, Twigfield uses the `CraftApiAutocomplete` & `TwigLanguageAutocomplete`, but it also includes an optional `EnvironmentVariableAutocomplete` which provides autocompletion of any Craft CMS [Environment Variables](https://craftcms.com/docs/4.x/config/#environmental-configuration) and [Aliases](https://craftcms.com/docs/4.x/config/#aliases).

If you want to use the `EnvironmentVariableAutocomplete` or a custom Autocomplete you write, you'll need to add a little PHP code to your plugin, module, or project:

```php
use nystudio107\twigfield\autocompletes\EnvironmentVariableAutocomplete;
use nystudio107\twigfield\events\RegisterTwigfieldAutocompletesEvent;
use nystudio107\twigfield\services\AutocompleteService;

Event::on(
    AutocompleteService::class,
    AutocompleteService::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES,
    function (RegisterTwigfieldAutocompletesEvent $event) {
        $event->types[] = EnvironmentVariableAutocomplete::class;
    }
);
```

The above code will add Environment Variable & Alias autocompletes to all of your Twigfield editors.

However, because you might have several instances of a Twigfield on the same page, and they each may provide separate Autocompletes, you may want to selectively add a custom Autocomplete only when the `fieldType` matches a specific.

Here's an example from the [Sprig plugin](https://github.com/putyourlightson/craft-sprig):

```php
use nystudio107\twigfield\events\RegisterTwigfieldAutocompletesEvent;
use nystudio107\twigfield\services\AutocompleteService;
use putyourlightson\sprig\plugin\autocompletes\SprigApiAutocomplete;

public const SPRIG_TWIG_FIELD_TYPE = 'SprigField';

Event::on(
    AutocompleteService::class,
    AutocompleteService::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES,
    function (RegisterTwigfieldAutocompletesEvent $event) {
        if ($event->fieldType === self::SPRIG_TWIG_FIELD_TYPE) {
            $event->types[] = SprigApiAutocomplete::class;
        }
    }
);
```

This ensures that the `SprigApiAutocomplete` Autocomplete will only be added when the `fieldType` passed into the Twigfield macros is set to `SprigField`.

## Writing a Custom Autocomplete


## Twig Template Validators

## Twigfield Roadmap

Some things to do, and ideas for potential features:

* 

Brought to you by [nystudio107](https://nystudio107.com/)
