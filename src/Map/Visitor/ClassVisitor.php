<?php

namespace Smt\FixtureGenerator\Map\Visitor;

use Smt\FixtureGenerator\Generator\GeneratorFactory;
use Smt\FixtureGenerator\Map\ClassMap;
use Smt\FixtureGenerator\Map\Visitor\Exception\NoAvailableFieldException;

/**
 * Parses mapping for class
 * @package Smt\FixtureGenerator\Map\Visitor
 * @author Kirill Saksin <kirillsaksin@yandex.ru>
 */
class ClassVisitor
{
    /**
     * @var ClassMap Mapping
     */
    private $map;

    /**
     * @var GeneratorFactory Generator
     */
    private $factory;

    /**
     * @var \ReflectionClass Class reflection
     */
    private $reflection;

    /**
     * Constructor
     * @param GeneratorFactory $factory Creates generators
     */
    public function __construct(GeneratorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Create mapping from flat config
     * @param array $mapping Flat config
     * @return ClassMap Mapping
     * @throws NoAvailableFieldException
     */
    public function visit(array $mapping)
    {
        $this->loadFile($mapping);
        $this->map = new ClassMap($mapping['class']);
        $this->reflection = new \ReflectionClass($mapping['class']);
        foreach ($mapping['fields'] as $fieldMapping) {
            $options = [];
            if (isset($fieldMapping['options'])) {
                $options = $fieldMapping['options'];
            }
            $generator = $this->factory->create($fieldMapping['type'], $options);
            if ($this->checkConstructor($fieldMapping)) {
                $index = $this->getConstructorParamIndex($fieldMapping);
                $this->map->addConstructorParam($generator, $index);
            } elseif ($this->checkProperty($fieldMapping)) {
                $propertyName = $this->getPropertyName($fieldMapping);
                $this->map->addProperty($generator, $propertyName);
            } elseif ($this->checkSetter($fieldMapping)) {
                $setterName = $this->getSetterName($fieldMapping);
                $this->map->addSetter([$generator], $setterName);
            } elseif ($this->reflection->hasMethod('__set')) {
                $this->map->addProperty($generator, $fieldMapping['name']);
            } else {
                throw new NoAvailableFieldException($fieldMapping['name']);
            }
        }
        return $this->map;
    }

    /**
     * Unifies name for comparison
     * @param string $name
     * @return string
     */
    private static function unifyName($name)
    {
        return str_replace('_', '', strtolower($name));
    }

    /**
     * Check if parameter is in constructor
     * @param array $fieldMapping Config
     * @return bool
     */
    private function checkConstructor(array $fieldMapping)
    {
        return $this->getConstructorParamIndex($fieldMapping) !== false;
    }


    /**
     * Get index of parameter in constructor
     * @param array $fieldMapping Config
     * @return int|bool
     */
    private function getConstructorParamIndex(array $fieldMapping)
    {
        if ($this->reflection->getConstructor() === null) {
            return false;
        }
        return array_search(self::unifyName($fieldMapping['name']), array_map(function (\ReflectionParameter $param) {
            return self::unifyName($param->getName());
        }, $this->reflection->getConstructor()->getParameters()));
    }

    /**
     * Check if class has public field
     * @param array $fieldMapping Config
     * @return bool
     */
    private function checkProperty(array $fieldMapping)
    {
        return $this->getPropertyIndex($fieldMapping) !== false;
    }

    /**
     * Get name of public field in class
     * @param array $fieldMapping Config
     * @return string
     */
    private function getPropertyName(array $fieldMapping)
    {
        return $this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC)[$this->getPropertyIndex($fieldMapping)]
            ->getName();
    }

    /**
     * Get index of public property
     * @param array $fieldMapping Config
     * @return int|bool
     */
    private function getPropertyIndex(array $fieldMapping)
    {
        return array_search(self::unifyName($fieldMapping['name']), array_map(function (\ReflectionProperty $param) {
            return self::unifyName($param->getName());
        }, $this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC)));
    }

    /**
     * Get index of setter method
     * @param array $fieldMapping Config
     * @return int|bool
     */
    private function getSetterIndex(array $fieldMapping)
    {
        return array_search('set' . self::unifyName($fieldMapping['name']), array_map(function (\ReflectionMethod $method) {
            return self::unifyName($method->getName());
        }, $this->reflection->getMethods(self::getMethodFilter())));
    }

    /**
     * Check if property can be set by setter method
     * @param array $fieldMapping
     * @return bool
     */
    private function checkSetter(array $fieldMapping)
    {
        return $this->getSetterIndex($fieldMapping) !== false;
    }

    /**
     * Get name of property setter method
     * @param array $fieldMapping Config
     * @return string
     */
    private function getSetterName(array $fieldMapping)
    {
        return $this->reflection->getMethods(self::getMethodFilter())[$this->getSetterIndex($fieldMapping)]->getName();
    }

    /**
     * Load class file if necessary
     * @param array $mapping Config
     */
    private function loadFile($mapping)
    {
        if (isset($mapping['file']) && is_readable($mapping['file'])) {
            require_once $mapping['file'];
        }
    }

    /**
     * Filter const for methods
     * @return int Method filter
     */
    private static function getMethodFilter()
    {
        return \ReflectionMethod::IS_PUBLIC;
    }
}
