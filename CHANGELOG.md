# Craft Twigfield Changelog

All notable changes to this project will be documented in this file.

## 1.0.6 - 2022.06.21
### Changed
* Better handling of object property docblocks

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
