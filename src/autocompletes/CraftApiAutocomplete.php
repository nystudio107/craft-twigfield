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
use craft\base\Element;
use nystudio107\twigfield\base\Autocomplete;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\types\AutocompleteTypes;
use nystudio107\twigfield\types\CompleteItemKind;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\di\ServiceLocator;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.0
 */
class CraftApiAutocomplete extends Autocomplete
{
    // Constants
    // =========================================================================

    const EXCLUDED_PROPERTY_NAMES = [
        'controller',
        'Controller',
        'CraftEdition',
        'CraftSolo',
        'CraftPro',
    ];
    const EXCLUDED_BEHAVIOR_NAMES = [
        'fieldHandles',
        'hasMethods',
        'owner',
    ];
    const ELEMENT_ROUTE_EXCLUDES = [
        'matrixblock',
        'globalset'
    ];
    const EXCLUDED_PROPERTY_REGEXES = [
        '^_',
    ];
    const EXCLUDED_METHOD_REGEXES = [
        '^_',
    ];
    const RECURSION_DEPTH_LIMIT = 10;

    // Public Properties
    // =========================================================================

    /**
     * @var string The name of the autocomplete
     */
    public $name = 'CraftApiAutocomplete';

    /**
     * @var string The type of the autocomplete
     */
    public $type = AutocompleteTypes::TwigExpressionAutocomplete;

    /**
     * @var string Whether the autocomplete should be parsed with . -delimited nested sub-properties
     */
    public $hasSubProperties = true;

    /**
     * @var array A key-value array of the Twig global variables to parse. If left empty, it will
     * default to the current Twig context global variables
     */
    public $twigGlobals = [];

    /**
     * @var array A key-value array of the Element Route variables (the injected `entry`, etc.
     * variable). If left empty, it will default to the current Element Route variables
     */
    public $elementRouteGlobals = [];

    /**
     * @var array A key-value array of additional global variables to parse for completions
     */
    public $additionalGlobals = [];

    // Public Methods
    // =========================================================================

    /**
     * @inerhitDoc
     */
    public function init(): void
    {
        if (empty($this->twigGlobals)) {
            $this->twigGlobals = Craft::$app->view->getTwig()->getGlobals();
        }
        if (empty($this->elementRouteGlobals)) {
            $this->elementRouteGlobals = $this->getElementRouteGlobals();
        }
    }

    /**
     * Core function that generates the autocomplete array
     */
    public function generateCompleteItems(): void
    {
        // Gather up all of the globals to parse
        $globals = array_merge(
            $this->twigGlobals,
            $this->elementRouteGlobals,
            $this->additionalGlobals,
            $this->overrideValues(),
        );
        foreach ($globals as $key => $value) {
            if (!in_array($key, self::EXCLUDED_PROPERTY_NAMES, true)) {
                $type = gettype($value);
                switch ($type) {
                    case 'object':
                        $this->parseObject($key, $value, 0);
                        break;

                    case 'array':
                    case 'boolean':
                    case 'double':
                    case 'integer':
                    case 'string':
                        $kind = CompleteItemKind::VariableKind;
                        $path = $key;
                        $normalizedKey = preg_replace("/[^A-Za-z]/", '', $key);
                        if (ctype_upper($normalizedKey)) {
                            $kind = CompleteItemKind::ConstantKind;
                        }
                        $this->addCompleteItem(new CompleteItem([
                            'detail' => $value,
                            'kind' => $kind,
                            'label' => $key,
                            'insertText' => $key,
                        ]), $path);
                        break;
                }
            }
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * Parse the object passed in, including any properties or methods
     *
     * @param string $name
     * @param $object
     * @param int $recursionDepth
     * @param string $path
     */
    protected function parseObject(string $name, $object, int $recursionDepth, string $path = ''): void
    {
        // Only recurse `RECURSION_DEPTH_LIMIT` deep
        if ($recursionDepth > self::RECURSION_DEPTH_LIMIT) {
            return;
        }
        $recursionDepth++;
        // Create the docblock factory
        $factory = DocBlockFactory::createInstance();

        $path = trim(implode('.', [$path, $name]), '.');
        // The class itself
        $this->getClassCompletion($object, $factory, $name, $path);
        // ServiceLocator Components
        $this->getComponentCompletion($object, $recursionDepth, $path);
        // Class properties
        $this->getPropertyCompletion($object, $factory, $recursionDepth, $path);
        // Class methods
        $this->getMethodCompletion($object, $factory, $path);
        // Behavior properties
        $this->getBehaviorCompletion($object, $factory, $recursionDepth, $path);
    }

    /**
     * @param $object
     * @param DocBlockFactory $factory
     * @param string $name
     * @param $path
     */
    protected function getClassCompletion($object, DocBlockFactory $factory, string $name, $path): void
    {
        try {
            $reflectionClass = new ReflectionClass($object);
        } catch (ReflectionException $e) {
            return;
        }
        // Information on the class itself
        $className = $reflectionClass->getName();
        $docs = $reflectionClass->getDocComment();
        if ($docs) {
            $docblock = $factory->create($docs);
            if ($docblock) {
                $summary = $docblock->getSummary();
                if (!empty($summary)) {
                    $docs = $summary;
                }
                $description = $docblock->getDescription()->render();
                if (!empty($description)) {
                    $docs = $description;
                }
            }
        }
        $this->addCompleteItem(new CompleteItem([
            'detail' => $className,
            'documentation' => $docs,
            'kind' => CompleteItemKind::ClassKind,
            'label' => $name,
            'insertText' => $name,
        ]), $path);
    }

    /**
     * @param $object
     * @param $recursionDepth
     * @param $path
     */
    protected function getComponentCompletion($object, $recursionDepth, $path): void
    {
        if ($object instanceof ServiceLocator) {
            foreach ($object->getComponents() as $key => $value) {
                $componentObject = null;
                try {
                    $componentObject = $object->get($key);
                } catch (InvalidConfigException $e) {
                    // That's okay
                }
                if ($componentObject) {
                    $this->parseObject($key, $componentObject, $recursionDepth, $path);
                }
            }
        }
    }

    /**
     * @param $object
     * @param DocBlockFactory $factory
     * @param $recursionDepth
     * @param string $path
     */
    protected function getPropertyCompletion($object, DocBlockFactory $factory, $recursionDepth, string $path): void
    {
        try {
            $reflectionClass = new ReflectionClass($object);
        } catch (ReflectionException $e) {
            return;
        }
        $reflectionProperties = $reflectionClass->getProperties();
        $customField = false;
        if ($object instanceof Behavior) {
            $customField = true;
        }
        $sortPrefix = $customField ? '~' : '~~';
        foreach ($reflectionProperties as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();
            // Exclude some properties
            $propertyAllowed = true;
            foreach (self::EXCLUDED_PROPERTY_REGEXES as $excludePattern) {
                $pattern = '`' . $excludePattern . '`i';
                if (preg_match($pattern, $propertyName) === 1) {
                    $propertyAllowed = false;
                }
            }
            if (in_array($propertyName, self::EXCLUDED_PROPERTY_NAMES, true)) {
                $propertyAllowed = false;
            }
            if ($customField && in_array($propertyName, self::EXCLUDED_BEHAVIOR_NAMES, true)) {
                $propertyAllowed = false;
            }
            // Process the property
            if ($propertyAllowed && $reflectionProperty->isPublic()) {
                $detail = "Property";
                $docblock = null;
                $docs = $reflectionProperty->getDocComment();
                if ($docs) {
                    $docblock = $factory->create($docs);
                    $docs = '';
                    if ($docblock) {
                        $summary = $docblock->getSummary();
                        if (!empty($summary)) {
                            $docs = $summary;
                        }
                        $description = $docblock->getDescription()->render();
                        if (!empty($description)) {
                            $docs = $description;
                        }
                    }
                }
                // Figure out the type
                if ($docblock) {
                    $tag = $docblock->getTagsByName('var');
                    if ($tag && isset($tag[0])) {
                        $detail = $tag[0];
                    }
                }
                if ($detail === "Property") {
                    if (preg_match('/@var\s+([^\s]+)/', $docs, $matches)) {
                        list(, $type) = $matches;
                        $detail = $type;
                    } else {
                        $detail = "Property";
                    }
                }
                if ($detail === "Property") {
                    if ((PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 4) || (PHP_MAJOR_VERSION >= 8)) {
                        if ($reflectionProperty->hasType()) {
                            $reflectionType = $reflectionProperty->getType();
                            if ($reflectionType instanceof ReflectionNamedType) {
                                $type = $reflectionType->getName();
                                $detail = $type;
                            }
                        }
                        if (PHP_MAJOR_VERSION >= 8) {
                            if ($reflectionProperty->hasDefaultValue()) {
                                $value = $reflectionProperty->getDefaultValue();
                                if (is_array($value)) {
                                    $value = json_encode($value);
                                }
                                if (!empty($value)) {
                                    $detail = "$value";
                                }
                            }
                        }
                    }
                }
                $thisPath = trim(implode('.', [$path, $propertyName]), '.');
                $label = $propertyName;
                $this->addCompleteItem(new CompleteItem([
                    'detail' => $detail,
                    'documentation' => $docs,
                    'kind' => $customField ? CompleteItemKind::FieldKind : CompleteItemKind::PropertyKind,
                    'label' => $label,
                    'insertText' => $label,
                    'sortText' => $sortPrefix . $label,
                ]), $thisPath);
                // Recurse through if this is an object
                if (isset($object->$propertyName) && is_object($object->$propertyName)) {
                    if (!$customField && !in_array($propertyName, self::EXCLUDED_PROPERTY_NAMES, true)) {
                        $this->parseObject($propertyName, $object->$propertyName, $recursionDepth, $path);
                    }
                }
            }
        }
    }

    /**
     * @param $object
     * @param DocBlockFactory $factory
     * @param string $path
     */
    protected function getMethodCompletion($object, DocBlockFactory $factory, string $path): void
    {
        try {
            $reflectionClass = new ReflectionClass($object);
        } catch (ReflectionException $e) {
            return;
        }
        $reflectionMethods = $reflectionClass->getMethods();
        foreach ($reflectionMethods as $reflectionMethod) {
            $methodName = $reflectionMethod->getName();
            // Exclude some properties
            $methodAllowed = true;
            foreach (self::EXCLUDED_METHOD_REGEXES as $excludePattern) {
                $pattern = '`' . $excludePattern . '`i';
                if (preg_match($pattern, $methodName) === 1) {
                    $methodAllowed = false;
                }
            }
            // Process the method
            if ($methodAllowed && $reflectionMethod->isPublic()) {
                $docblock = null;
                $docs = $reflectionMethod->getDocComment();
                if ($docs) {
                    $docblock = $factory->create($docs);
                    if ($docblock) {
                        $summary = $docblock->getSummary();
                        if (!empty($summary)) {
                            $docs = $summary;
                        }
                        $description = $docblock->getDescription()->render();
                        if (!empty($description)) {
                            $docs = $description;
                        }
                    }
                }
                $detail = $methodName . '(';
                $params = $reflectionMethod->getParameters();
                $paramList = [];
                foreach ($params as $param) {
                    if ($param->hasType()) {
                        $reflectionType = $param->getType();
                        if ($reflectionType instanceof ReflectionUnionType) {
                            $unionTypes = $reflectionType->getTypes();
                            $typeName = '';
                            foreach ($unionTypes as $unionType) {
                                $typeName .= '|' . $unionType->getName();
                            }
                            $typeName = trim($typeName, '|');
                            $paramList[] = $typeName . ': ' . '$' . $param->getName();
                        } else {
                            $paramList[] = $param->getType()->getName() . ': ' . '$' . $param->getName();
                        }
                    } else {
                        $paramList[] = '$' . $param->getName();
                    }
                }
                $detail .= implode(', ', $paramList) . ')';
                $thisPath = trim(implode('.', [$path, $methodName]), '.');
                $label = $methodName . '()';
                $docsPreamble = '';
                // Figure out the type
                if ($docblock) {
                    $tags = $docblock->getTagsByName('param');
                    if ($tags) {
                        $docsPreamble = "Parameters:\n\n";
                        foreach ($tags as $tag) {
                            $docsPreamble .= $tag . "\n";
                        }
                        $docsPreamble .= "\n";
                    }
                }
                $this->addCompleteItem(new CompleteItem([
                    'detail' => $detail,
                    'documentation' => $docsPreamble . $docs,
                    'kind' => CompleteItemKind::MethodKind,
                    'label' => $label,
                    'insertText' => $label,
                    'sortText' => '~~~' . $label,
                ]), $thisPath);
            }
        }
    }

    /**
     * @param $object
     * @param DocBlockFactory $factory
     * @param $recursionDepth
     * @param string $path
     */
    protected function getBehaviorCompletion($object, DocBlockFactory $factory, $recursionDepth, string $path): void
    {
        if ($object instanceof Element) {
            $behaviorClass = $object->getBehavior('customFields');
            if ($behaviorClass) {
                $this->getPropertyCompletion($behaviorClass, $factory, $recursionDepth, $path);
            }
        }
    }

    // Private Methods
    // =========================================================================

    /**
     * Add in the element types that could be injected as route variables
     *
     * @return array
     */
    private function getElementRouteGlobals(): array
    {
        $routeVariables = [];
        $elementTypes = Craft::$app->elements->getAllElementTypes();
        foreach ($elementTypes as $elementType) {
            /* @var Element $elementType */
            $key = $elementType::refHandle();
            if (!empty($key) && !in_array($key, self::ELEMENT_ROUTE_EXCLUDES)) {
                $routeVariables[$key] = new $elementType();
            }
        }

        return $routeVariables;
    }

    /**
     * Override certain values that we always want hard-coded
     *
     * @return array
     */
    private function overrideValues(): array
    {
        return [
            // Set the nonce to a blank string, as it changes on every request
            'nonce' => '',
        ];
    }
}
