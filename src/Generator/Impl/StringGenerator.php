<?php

namespace Smt\FixtureGenerator\Generator\Impl;

use Smt\FixtureGenerator\Generator\Generator;
use Smt\FixtureGenerator\Util\Options;

/**
 * String generator
 * @package Smt\FixtureGenerator\Generator\Impl
 * @author Kirill Saksin <kirillsaksin@yandex.ru>
 */
class StringGenerator implements Generator
{

    /**
     * @var array Default configuration
     */
    private static $defaultConfig = [
        'length' => ['from' => 5, 'to' => 10],
        'alphabet' => [
            'a-z',
            'A-Z',
            '0-9',
        ],
        'raw' => false,
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
     * @var bool Whether to generate just strings without valid PHP syntax
     */
    private $raw;


    /**
     * Constructor
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $realConfig = Options::merge(self::$defaultConfig, $config);
        $this->length = $realConfig['length'];
        $this->raw = $realConfig['raw'];
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
        $format = '%s';
        if (!$this->raw) {
            $format = '\'%s\'';
        }
        $alphabetLen = count($this->alphabet) - 1;
        $length = $this->length;
        if (is_array($length)) {
            $length = rand($length['from'], $length['to']);
        }
        for ($i = 0; $i < $length; $i++) {
            $string .= $this->alphabet[rand(0, $alphabetLen)];
        }
        return sprintf($format, $string);
    }
}
