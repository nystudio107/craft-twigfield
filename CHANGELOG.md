# Craft Twigfield Changelog

All notable changes to this project will be documented in this file.

## 1.0.2 - UNRELEASED
### Fixed
* Fixed an issue where autocomplete of nested properties wouldn't work if there was no space after a `{` in Twig

## 1.0.1 - 2022.06.13
### Added
* Added `text()` and `textField()` macros that create a single-line Twig editor for simple Twig expressions
* Added `$additionalGlobals` to the `CraftApiAutocomplete` class so that classes extending it can add their own global variables to be parsed for autocomplete items

## 1.0.0 - 2022.06.13
### Added
* Initial release
