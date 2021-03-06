<?php

namespace Smt\FixtureGenerator\Generator\Impl;

use Smt\FixtureGenerator\Generator\Generator;
use Smt\FixtureGenerator\Util\Options;

/**
 * IP-address generator
 * @package Smt\FixtureGenerator\Generator\Impl
 * @author Kirill Saksin <kirillsaksin@yandex.ru>
 * @SuppressWarnings(PHPMD.ShortVariable) $to, $ip
 */
class IpGenerator implements Generator
{

    /**
     * @var string[] Default configuration
     */
    private static $defaultConfig = [
        'version' => 4,
        'from' => '0.0.0.0',
        'to' => '255.255.255.255',
    ];

    /**
     * @var int IP version
     */
    private $version;

    /**
     * @var string Lower IP bound
     */
    private $from;

    /**
     * @var string Upper IP bound
     */
    private $to;

    /**
     * Constructor
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $realConfig = Options::merge(self::$defaultConfig, $config);
        $this->version = $realConfig['version'];
        $this->from = $realConfig['from'];
        $this->to = $realConfig['to'];
    }

    /** {@inheritdoc} */
    public function generate($count)
    {
        if ($count > 1) {
            $ips = [];
            for ($i = 0; $i < $count; $i++) {
                $ips[] = $this->doGenerate();
            }
            return '\'[' . implode('\'', $ips) . '\']';
        }
        return $this->doGenerate();
    }

    /**
     * Single IP generation
     * @return string Generated IP
     */
    private function doGenerate()
    {
        $ip = [];
        $delimiter = $this->version === 4 ? '.' : ':';
        $froms = explode($delimiter, $this->from);
        $tos = explode($delimiter, $this->to);
        foreach ($froms as $key => $from) {
            $ip[] = rand(intval($from), intval($tos[$key]));
        }
        return '\'' . implode($delimiter, $ip) . '\'';
    }
}
