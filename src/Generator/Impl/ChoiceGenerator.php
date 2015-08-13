<?php

namespace Smt\FixtureGenerator\Generator\Impl;

use Smt\FixtureGenerator\Generator\Generator;
use Smt\FixtureGenerator\Util\Options;

/**
 * Generate one element from specified list
 * @package Smt\FixtureGenerator\Generator\Impl
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class ChoiceGenerator implements Generator
{
    /**
     * @var array Choices
     */
    private $choices;

    /**
     * @var array Default configuration
     */
    private static $defaultConfig = [
        'list' => [],
    ];

    /**
     * Constructor
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $realConfig = Options::merge(self::$defaultConfig, $config);
        $this->choices = $realConfig['list'];
    }

    /** {@inheritdoc} */
    public function generate($count)
    {
        if ($count > 1) {
            $code = [];
            for ($i = 0; $i < $count; $i++) {
                $code[] = $this->doGenerate();
            }
            return '[' . implode(', ', $code) . ']';
        } else {
            return $this->doGenerate();
        }
    }

    /**
     * Real generation
     * @return mixed
     */
    private function doGenerate()
    {
        $data = $this->choices;
        shuffle($data);
        return $data[0];
    }
}
