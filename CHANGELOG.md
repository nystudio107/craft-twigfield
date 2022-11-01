# Craft Twigfield Changelog

All notable changes to this project will be documented in this file.

# DEPRECATED

Twigfield is now deprecated; please use [nystudio107/craft-code-editor](https://github.com/nystudio107/craft-code-editor) instead, which is a general purpose code editor that also does Twig & autocompletes.

## 1.0.20 - UNRELEASED
### Added
* Added all language workers to the build / bundle process

### Changed
* Refactored `twigfield.js` to TypeScript
* Move the language icons to a separate `language-icons.ts` file
* Remove the transparent background CSS style to allow for theming

## 1.0.19 - 2022.10.26
### Fixed
* Fixed an issue that didn't properly encode `twigFieldOptions` for a JavaScript context, resulting in a broken field in some cases ([#1225](https://github.com/nystudio107/craft-seomatic/issues/1225))

## 1.0.18 - 2022.10.25
### Added
* Manually handle Tab & Shift-Tab for single line Twigfields to allow tabbing to other fields in a form

## 1.0.17 - 2022.10.23
### Added
* Added a better Twig indicator icon, along with a `title` attribute for a tooltip indicator, and #a11y improvements ([#5](https://github.com/nystudio107/craft-twigfield/pull/5))

### Changed
* Set both `alwaysConsumeMouseWheel` & `handleMouseWheel` to `false` in the default Monaco Editor config to avoid it consuming mouse wheel events that prevent scrolling pages with Twigfield fields ref: ([#1853](https://github.com/microsoft/monaco-editor/issues/1853))

## 1.0.16 - 2022.10.18
### Changed
* Moved `craftcms/cms` to `require-dev`

## 1.0.15 - 2022.10.17
### Fixed
* Fixed an issue that caused Twigfield to throw an exception if you were running < PHP 8 ([#1220](https://github.com/nystudio107/craft-seomatic/issues/1220))

## 1.0.14 - 2022.10.13
### Fixed
* Fixed an issue where `getCustomFields()` was being called in Craft 3, where it doesn't exist

## 1.0.13 - 2022.10.13
### Added 
* Added `monaco-editor-inline-frame` built-in style for an inline editor in a table cell (or elsewhere that no chrome is desired)
* Added `SectionShorthandFieldsAutocomplete` to provide shorthand autocomplete items for Craft sections
* Added conditionals to the `ObjectParserAutocomplete` abstract class so that child classes can determine exactly what gets parsed by overriding properties
* Added the ability to have placeholder text for the Twigfield editor
* Allow the Twig environment to be passed down to the `TwigLanguageAutocomplete` Autocomplete via DI
* Change constants to properties for the sort prefixes in `ObjectParserAutocomplete`  to allow child classes to override the settings

### Changed
* Invalidate `SectionShorthandFieldsAutocomplete` caches whenever any field layout is edited
* Add in magic getter properties that are defined only in the `@property` docblock annotation

## 1.0.12 - 2022.10.04
### Added
* Add `ObjectAutocomplete` class to allow for easily adding all of the properties of an object as autocomplete items
* Add missing Twig tags `else`, `elseif`, `endblock` & `endif`
* Allow the `twigfieldOptions` config object to be passed into the Twig macros
* Include a hash of the `twigfieldOptions` in the cache key used for the autocomplete

### Changed
* Refactor to `ObjectParserAutocomplete` & `ObjectParserInterface`

## 1.0.11 - 2022.08.24
### Changed
* Remove `FluentModel` class and replace the magic method setter with fluent setter methods in `CompleteItem`

## 1.0.10 - 2022.08.23
### Changed
* Add `allow-plugins` to `composer.json` so CI can work

### Fixed
* Fixed an issue where an exception could be thrown during the bootstrap process in earlier versions of Yii2 due to `$id` not being set

## 1.0.9 - 2022.06.24
### Fixed
* Instead of attempting to convert an array into a string, JSON-encode the keys of the array for the value

## 1.0.8 - 2022.06.23
### Fixed
* Fixed an issue that could cause an exception to be thrown after first install/update to a plugin that uses Twigfield, which prevented the module from loading ([#2](https://github.com/nystudio107/craft-twigfield/issues/2)) ([#1161](https://github.com/nystudio107/craft-seomatic/issues/1161))

## 1.0.7 - 2022.06.22
### Fixed
* Fixed an issue that could cause the autocomplete endpoint to 404 if the `actionUrl` already contains URL parameters ([#1](https://github.com/nystudio107/craft-twigfield/pull/1))

## 1.0.6 - 2022.06.21
### Changed
* Better handling of object property docblocks in the `CraftApiAutocomplete`
* The `CraftApiAutocomplete` now typecasts properties to `string` to ensure they validate

## 1.0.5 - 2022.06.21
### Changed
* Only issue an XHR for autocomplete items of the specified `fieldType` if they haven't been added already, for better performance with multiple Twigfield instances on a single page

## 1.0.4 - 2022.06.20
### Changed
* Handle cases where there is no space between the `{{` opening brackets of a Twig expression so nested properties autocomplete there, too
* Sort environment variables below other autocompletes
* Tweak the CSS to allow it to fit into the encompassing `<div>` better

## 1.0.3 - 2022.06.18
### Added
* Added the ability to pass in a config array to autocomplete classes via the `AutocompleteService::EVENT_REGISTER_TWIGFIELD_AUTOCOMPLETES` event
* Added the `$hasSubProperties` property to the Autocomplete model, to indicate whether the autocomplete returns nested sub-properties such as `foo.bar.baz`
* Added the ability to pass in the `twigGlobals` & `elementRouteGlobals` properties via dependency injection to the `CraftApiAutocomplete` autocomplete

### Changed
* Removed errant logging

## 1.0.2 - 2022.06.15
### Fixed
* Fixed an issue where autocomplete of nested properties wouldn't work if there was no space after a `{` in Twig
* Fixed an issue where `GeneralAutocompletes` were applied when we were in a sub-property, which resulted in JS errors

## 1.0.1 - 2022.06.13
### Added
* Added `text()` and `textField()` macros that create a single-line Twig editor for simple Twig expressions
* Added `$additionalGlobals` to the `CraftApiAutocomplete` class so that classes extending it can add their own global variables to be parsed for autocomplete items

## 1.0.0 - 2022.06.13
### Added
* Initial release
