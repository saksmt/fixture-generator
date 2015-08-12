<?php

namespace Smt\FixtureGenerator\Generator;

use Smt\FixtureGenerator\Generator\Impl as Generators;

/**
 * Generator factory
 * @package Smt\FixtureGenerator\Generator
 * @author Kirill Saksin <kirill.saksin@billing.ru>
 */
class GeneratorFactory
{
    protected static $registeredGenerators = [
        'string' => Generators\StringGenerator::class,
        'ip' => Generators\IpGenerator::class,
    ];

    /**
     * Create new generator of specified type (name)
     * @param string $name Generator name
     * @param array $config Generator configuration
     * @return Generator Generator instance
     * @throws GeneratorNotFoundException
     */
    public function create($name, array $config)
    {
        if (!isset(self::$registeredGenerators[$name])) {
            throw new GeneratorNotFoundException(sprintf('Generator with name "%s" not registered!', $name));
        }
        $generatorClass = static::$registeredGenerators[$name];
        return new $generatorClass($config);
    }

    /**
     * Register new generator
     * @param string $name Generator name
     * @param string $class Generator class name, must implement @ling Generator interface
     * @throws UnsupportedGeneratorTypeException
     */
    public static function registerGenerator($name, $class)
    {
        $ref = new \ReflectionClass($class);
        if (!$ref->implementsInterface(Generator::class)) {
            throw new UnsupportedGeneratorTypeException($name);
        }
        self::$registeredGenerators[$name] = $class;
    }
}
