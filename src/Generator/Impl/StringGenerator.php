<?php

namespace Smt\FixtureGenerator\Generator\Impl;

use Smt\FixtureGenerator\Generator\Generator;

/**
 * String generator
 * @package Smt\FixtureGenerator\Generator\Impl
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class StringGenerator implements Generator
{

    /**
     * @var array Default configuration
     */
    private static $defaultConfig = [
        'length' => 5,
        'alphabet' => [
            'a-z',
            'A-Z',
            '0-9',
        ],
    ];

    /**
     * @var int String len
     */
    private $length;

    /**
     * @var string[] String alphabet (allowed symbols)
     */
    private $alphabet;

    /**
     * Constructor
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $realConfig = array_merge_recursive(self::$defaultConfig, $config);
        $this->length = $realConfig['length'];
        $alphabet = [];
        foreach ($realConfig['alphabet'] as $diapason) {
            if (preg_match('/^(.*?)-(.*?)$/', $diapason)) {
                $arrayDiapason = explode('-', $diapason);
                $alphabet = array_merge($alphabet, range($arrayDiapason[0], $arrayDiapason[1]));
            } else {
                $alphabet = array_merge($alphabet, str_split($diapason));
            }
        }
        $this->alphabet = $alphabet;
    }

    /** {@inheritdoc} */
    public function generate($count)
    {
        if ($count > 1) {
            $strings = [];
            for ($i = 0; $i < $count; $i++) {
                $strings[] = $this->doGenerate();
            }
            return '[\'' . implode('\', ', $strings) . '\']';
        }
        return $this->doGenerate();
    }

    /**
     * Generate random string
     * @return string Generated string
     */
    private function doGenerate()
    {
        $string = '';
        $alphabetLen = count($this->alphabet);
        for ($i = 0; $i < $this->length; $i++) {
            $string .= $this->alphabet[rand(0, $alphabetLen)];
        }
        return $string;
    }
}
