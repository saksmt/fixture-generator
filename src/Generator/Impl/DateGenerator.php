<?php

namespace Smt\FixtureGenerator\Generator\Impl;

use Smt\FixtureGenerator\Generator\Generator;
use Smt\FixtureGenerator\Util\Options;

/**
 * Generate DateTime
 * @package Smt\FixtureGenerator\Generator\Impl
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 * @SuppressWarnings(PHPMD.ShortVariable) $to
 */
class DateGenerator implements Generator
{
    /**
     * @var array Default configuration
     */
    private static $defaultConfig = [
        'from' => 0,
        'to' => 'inf',
        'format' => 'd.m.y H:i:s',
    ];

    /**
     * @var int Start date
     */
    private $from;

    /**
     * @var int End date
     */
    private $to;

    /**
     * @var string Input date format
     */
    private $format;

    /**
     * Constructor
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $realConfig = Options::merge(self::$defaultConfig, $config);
        $this->format = $realConfig['format'];
        $this->from = $this->parseDate($realConfig['from']);
        $this->to = $this->parseDate($realConfig['to']);
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
     * @return string
     */
    private function doGenerate()
    {
        return 'new \DateTime(' . rand($this->from, $this->to) . ')';
    }

    /**
     * @param int|string $date Config date representation
     * @return int
     */
    private function parseDate($date)
    {
        if (strtolower($date) === 'now') {
            return (new \DateTime())->getTimestamp();
        } elseif (strtolower($date) === 'inf') {
            return PHP_INT_MAX;
        } elseif ($date === 0) {
            return 0;
        } else {
            return \DateTime::createFromFormat($this->format, $date)->getTimestamp();
        }
    }
}
