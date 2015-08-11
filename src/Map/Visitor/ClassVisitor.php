<?php

namespace Smt\FixtureGenerator\Map\Visitor;

use Smt\FixtureGenerator\Map\ClassMap;

/**
 * Parses mapping for class
 * @package Smt\FixtureGenerator\Map\Visitor
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class ClassVisitor
{
    private $map;
    private $factory;
    const METHOD_FILTER = \ReflectionMethod::IS_PUBLIC &
    !\ReflectionMethod::IS_ABSTRACT &
    !\ReflectionMethod::IS_STATIC;

    /**
     * @var \ReflectionClass
     */
    private $reflection;

    public function __construct(GeneratorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $name
     * @return string
     */
    private static function unifyName($name)
    {
        return str_replace('_', '', strtolower($name));
    }

    public function setClassMap(ClassMap $map)
    {
        $this->map = $map;
    }

    public function visit(array $mapping)
    {
        require_once $mapping['file'];
        $this->reflection = new \ReflectionClass($mapping['class']);
        foreach ($mapping['fields'] as $fieldMapping) {
            $generator = $this->factory->create($fieldMapping['type'], $fieldMapping['options']);
            if ($this->checkConstructor($fieldMapping)) {
                $index = $this->getConstructorParamIndex($fieldMapping);
                $this->map->addConstructorParam($generator, $index);
            } elseif ($this->checkProperty($fieldMapping)) {
                $propertyName = $this->getPropertyName($fieldMapping);
                $this->map->addProperty($generator, $propertyName);
            } elseif ($this->checkSetter($fieldMapping)) {
                $setterName = $this->getSetterName($fieldMapping);
                $this->map->addSetter($generator, $setterName);
            } elseif ($this->reflection->hasMethod('__set')) {
                $this->map->addProperty($generator, $fieldMapping['name']);
            } else {
                throw new NoAvailableFieldException($fieldMapping['name']);
            }
        }
    }

    private function checkConstructor(array $fieldMapping)
    {
        return $this->getConstructorParamIndex($fieldMapping) !== false;
    }

    private function getConstructorParamIndex(array $fieldMapping)
    {
        return array_search(self::unifyName($fieldMapping['name']), array_map(function (\ReflectionParameter $param) {
            return self::unifyName($param->getName());
        }, $this->reflection->getConstructor()->getParameters()));
    }

    private function checkProperty(array $fieldMapping)
    {
        return $this->getPropertyIndex($fieldMapping) !== false;
    }

    private function getPropertyName(array $fieldMapping)
    {
        return $this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC)[$this->getPropertyIndex($fieldMapping)];
    }

    /**
     * @param array $fieldMapping
     * @return mixed
     */
    private function getPropertyIndex(array $fieldMapping)
    {
        return array_search(self::unifyName($fieldMapping['name']), array_map(function (\ReflectionProperty $param) {
            return self::unifyName($param->getName());
        }, $this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC)));
    }

    private function getSetterIndex(array $fieldMapping)
    {
        return array_search('set' . self::unifyName($fieldMapping['name']), array_map(function (\ReflectionMethod $method) {
            return self::unifyName($method->getName());
        }, $this->reflection->getMethods(self::METHOD_FILTER)));
    }

    private function checkSetter(array $fieldMapping)
    {
        return $this->getSetterIndex($fieldMapping) !== false;
    }

    private function getSetterName(array $fieldMapping)
    {
        return $this->reflection->getMethods(self::METHOD_FILTER)[$this->getSetterIndex($fieldMapping)];
    }
}
