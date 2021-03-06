# Craft Twigfield Changelog

All notable changes to this project will be documented in this file.

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
