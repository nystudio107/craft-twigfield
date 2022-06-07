<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\types;

/**
 * Based on: https://microsoft.github.io/monaco-editor/api/enums/monaco.languages.completionitemkind.html
 *
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
abstract class CompleteItemKind
{
    // Constants
    // =========================================================================

    // Faux enum, No proper enums until PHP 8.1, and no constant visibility until PHP 7.1
    const ClassKind = 5;
    const ColorKind = 19;
    const ConstantKind = 14;
    const ConstructorKind = 2;
    const CustomcolorKind = 22;
    const EnumKind = 15;
    const EnumMemberKind = 16;
    const EventKind = 10;
    const FieldKind = 3;
    const FileKind = 20;
    const FolderKind = 23;
    const FunctionKind = 1;
    const InterfaceKind = 7;
    const IssueKind = 26;
    const KeywordKind = 17;
    const MethodKind = 0;
    const ModuleKind = 8;
    const OperatorKind = 11;
    const PropertyKind = 9;
    const ReferenceKind = 21;
    const SnippetKind = 27;
    const StructKind = 6;
    const TextKind = 18;
    const KindParameterKind = 24;
    const UnitKind = 12;
    const UserKind = 25;
    const ValueKind = 13;
    const VariableKind = 4;
}
