<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\base;

use craft\base\Element;
use nystudio107\twigfield\models\CompleteItem;
use nystudio107\twigfield\types\CompleteItemKind;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionUnionType;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\di\ServiceLocator;

/**
 * @author    nystudio107
 * @package   twigfield
 * @since     1.0.12
 */
abstract class ObjectParserAutocomplete extends Autocomplete implements ObjectParserInterface
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
     * @var bool If the class itself should be parsed for complete items
     */
    public $parseClass = true;

    /**
     * @var bool If any ServiceLocator components should be parsed for complete items
     */
    public $parseComponents = true;

    /**
     * @var bool If the class properties should be parsed for complete items
     */
    public $parseProperties = true;

    /**
     * @var bool If the class methods should be parsed for complete items
     */
    public $parseMethods = true;

    /**
     * @var bool If the class behaviors should be parsed for complete items
     */
    public $parseBehaviors = true;

    /**
     * @var string Prefix for custom (behavior) properties, for the complete items sort
     */
    public $customPropertySortPrefix = '~';

    /**
     * @var string Prefix for properties, for the complete items sort
     */
    public $propertySortPrefix = '~~';

    /**
     * @var string Prefix for methods, for the complete items sort
     */
    public $methodSortPrefix = '~~~';

    // Public Methods
    // =========================================================================

    /**
     * @inerhitdoc
     */
    public function parseObject(string $name, $object, int $recursionDepth, string $path = ''): void
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
        if ($this->parseClass) {
            $this->getClassCompletion($object, $factory, $name, $path);
        }
        // ServiceLocator Components
        if ($this->parseComponents) {
            $this->getComponentCompletion($object, $recursionDepth, $path);
        }
        // Class properties
        if ($this->parseProperties) {
            $this->getPropertyCompletion($object, $factory, $recursionDepth, $path);
        }
        // Class methods
        if ($this->parseMethods) {
            $this->getMethodCompletion($object, $factory, $path);
        }
        // Behavior properties
        if ($this->parseBehaviors) {
            $this->getBehaviorCompletion($object, $factory, $recursionDepth, $path);
        }
    }

    // Protected Methods
    // =========================================================================

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
        $docs = $this->getDocs($reflectionClass, $factory);
        CompleteItem::create()
            ->detail((string)$className)
            ->documentation((string)$docs)
            ->kind(CompleteItemKind::ClassKind)
            ->label((string)$name)
            ->insertText((string)$name)
            ->add($this, $path);
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
        $sortPrefix = $customField ? $this->customPropertySortPrefix : $this->propertySortPrefix;
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
                    $summary = $docblock->getSummary();
                    if (!empty($summary)) {
                        $docs = $summary;
                    }
                    $description = $docblock->getDescription()->render();
                    if (!empty($description)) {
                        $docs = $description;
                    }
                }
                // Figure out the type
                if ($docblock) {
                    $tag = $docblock->getTagsByName('var');
                    if ($tag && isset($tag[0])) {
                        $docs = $tag[0];
                    }
                }
                if (preg_match('/@var\s+([^\s]+)/', $docs, $matches)) {
                    list(, $type) = $matches;
                    $detail = $type;
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
                        if ((PHP_MAJOR_VERSION >= 8) && $reflectionProperty->hasDefaultValue()) {
                            $value = $reflectionProperty->getDefaultValue();
                            if (is_array($value)) {
                                $value = json_encode($value);
                            }
                            if (!empty($value)) {
                                $detail = (string)$value;
                            }
                        }
                    }
                }
                $thisPath = trim(implode('.', [$path, $propertyName]), '.');
                $label = $propertyName;
                CompleteItem::create()
                    ->detail((string)$detail)
                    ->documentation((string)$docs)
                    ->kind($customField ? CompleteItemKind::FieldKind : CompleteItemKind::PropertyKind)
                    ->label((string)$label)
                    ->insertText((string)$label)
                    ->sortText((string)$sortPrefix . (string)$label)
                    ->add($this, $thisPath);
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
                $docs = $this->getDocs($reflectionMethod, $factory);
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
                CompleteItem::create()
                    ->detail((string)$detail)
                    ->documentation((string)$docsPreamble . (string)$docs)
                    ->kind(CompleteItemKind::MethodKind)
                    ->label((string)$label)
                    ->insertText((string)$label)
                    ->sortText($this->methodSortPrefix . (string)$label)
                    ->add($this, $thisPath);
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

    /**
     * Try to get the best documentation block we can
     *
     * @param ReflectionClass|ReflectionMethod $reflection
     * @param DocBlockFactory $factory
     * @return string
     */
    protected function getDocs($reflection, DocBlockFactory $factory): string
    {
        $docs = $reflection->getDocComment();
        if ($docs) {
            $docblock = $factory->create($docs);
            $summary = $docblock->getSummary();
            if (!empty($summary)) {
                $docs = $summary;
            }
            $description = $docblock->getDescription()->render();
            if (!empty($description)) {
                $docs = $description;
            }
        }

        return $docs ?: '';
    }
}
