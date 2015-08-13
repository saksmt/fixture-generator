<?php

namespace Smt\FixtureGenerator\Generator;

use Smt\FixtureGenerator\Map\Visitor\ClassVisitor;
use Smt\FixtureGenerator\Map\Visitor\Exception\NoAvailableFieldException;

/**
 * Generates fixtures based on mappings
 * @package Smt\FixtureGenerator\Generator
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class FixtureGenerator
{
    /**
     * @var array Mappings
     */
    private $mappings;

    /**
     * @var ClassVisitor Mapping visitor
     */
    private $visitor;

    /**
     * @var int Fixtures count
     */
    private $count;

    /**
     * Constructor
     * @param array $mappings Mappings
     */
    public function __construct(array $mappings)
    {
        $this->mappings = $mappings;
        $this->visitor = new ClassVisitor(new GeneratorFactory());
    }

    /**
     * Generate fixtures based on mappings
     * @param int $count Fixtures count
     * @return string Fixtures
     */
    public function generate($count)
    {
        $this->count = $count;
        if (count($this->mappings) === 1) {
            return '<?php' . PHP_EOL . 'return ' . $this->doGenerate(array_shift($this->mappings)) . ';';
        } else {
            $code = [];
            foreach ($this->mappings as $className => $mapping) {
                $code[] = '\'' . $className . '\' => ' . $this->doGenerate($mapping) . ',';
            }
            return '<?php' . PHP_EOL . 'return [' . implode(PHP_EOL, $code) . '];';
        }
    }

    /**
     * Real generation
     * @param array $mapping Mapping config
     * @return string Code
     * @throws NoAvailableFieldException
     */
    private function doGenerate(array $mapping)
    {
        return $this->visitor->visit($mapping)->generate($this->count);
    }
}
