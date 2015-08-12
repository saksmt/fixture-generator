<?php

namespace Smt\FixtureGenerator\Map;
use Smt\FixtureGenerator\Generator\Generator;

/**
 * Represents mapping to class
 * @package Smt\FixtureGenerator\Map
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class ClassMap
{
    /**
     * @var Generator[] Properties mapping
     */
    private $properties = [];

    /**
     * @var Generator[] Constructor parameters mapping
     */
    private $construct = [];

    /**
     * @var Generator[] Method parameters mapping
     */
    private $methods = [];

    /**
     * @var string Class name
     */
    private $className;

    /**
     * Set class name
     * @param string $className Class name
     * @return ClassMap This instance
     */
    public function setClass($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * Add constructor parameter
     * @param Generator $generator Generator
     * @param int $index Parameter index
     * @return ClassMap This instance
     */
    public function addConstructorParam(Generator $generator, $index)
    {
        $this->construct[$index] = $generator;
        return $this;
    }

    /**
     * Add property
     * @param Generator $generator Generator
     * @param string $propertyName Property name
     * @return ClassMap This instance
     */
    public function addProperty(Generator $generator, $propertyName)
    {
        $this->properties[$propertyName] = $generator;
        return $this;
    }

    /**
     * Add setter method call
     * @param Generator[] $generators Method arguments
     * @param string $setterName Setter method name
     * @return ClassMap This instance
     */
    public function addSetter(array $generators, $setterName)
    {
        $this->methods[$setterName] = $generators;
        return $this;
    }

    /**
     * Generate source code
     * @param int $count Number of instances
     * @return string generated code
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function generate($count)
    {
        $variableNameTemplate = sprintf('_%s', md5($this->className));
        $code = 'call_user_func(function () {' . PHP_EOL;
        $code .= '$data = [];' . PHP_EOL;
        $runGenerator = function (Generator $generator) {
            return $generator->generate(1);
        };
        foreach (range(0, $count) as $index) {
            /* @noinspection PhpUnusedLocalVariableInspection */
            $variableName = $variableNameTemplate . $index;
            /* @noinspection PhpUnusedLocalVariableInspection */
            $constructorArgs = array_map($runGenerator, $this->construct);
            /* @noinspection PhpUnusedLocalVariableInspection */
            $properties = array_map($runGenerator, $this->properties);
            /* @noinspection PhpUnusedLocalVariableInspection */
            $methods = array_map(function ($arguments) use ($runGenerator) {
                return array_map($runGenerator, $arguments);
            }, $this->methods);
            ob_start();
            require_once dirname(dirname(__DIR__)) . '/res/class.tpl';
            $code .= ob_get_contents();
            ob_end_clean();
        }
        if ($count > 1) {
            $code .= 'return $data;' . PHP_EOL;
        } else {
            $code .= 'return array_shift($data);';
        }
        return $code . '})';
    }
}
