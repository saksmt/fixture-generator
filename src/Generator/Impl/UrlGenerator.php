<?php

namespace Smt\FixtureGenerator\Generator\Impl;

use Smt\FixtureGenerator\Generator\Generator;

/**
 * Generates urls
 * @package Smt\FixtureGenerator\Generator\Impl
 * @author Kirill Saksin <kirillsaksin@yandex.ru>
 */
class UrlGenerator implements Generator
{
    /**
     * @var StringGenerator Generates URL part
     */
    private $urlGenerator;

    /**
     * @var ChoiceGenerator Generates URL schema
     */
    private $schemaGenerator;

    /**
     * @var Generator Generates host
     */
    private $hostGenerator;

    /**
     * Constructor
     * @param array $config Configuration
     */
    public function __construct(array $config)
    {
        $this->urlGenerator = new StringGenerator([
            'raw' => 1,
            'alphabet' => [
                '/.?&',
                'a-z',
                'A-Z',
                '0-9',
            ],
        ]);
        if (isset($config['hosts'])) {
            $this->hostGenerator = new ChoiceGenerator(['list' => $config['hosts']]);
        } else {
            $this->hostGenerator = new StringGenerator([
                'raw' => 1,
                'alphabet' => [
                    '.',
                    'a-z',
                    'A-Z',
                    '0-9',
                ],
            ]);
        }
        $schema = ['http', 'https'];
        if (isset($config['schema'])) {
            $schema = [$config['schema']];
        }
        $this->schemaGenerator = new ChoiceGenerator(['list' => $schema]);
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
     * Real generator
     * @return string URL
     */
    private function doGenerate()
    {
        $format = '\'%s://%s/%s\'';
        $host = $this->hostGenerator->generate(1);
        $url = $this->urlGenerator->generate(1);
        $schema = $this->schemaGenerator->generate(1);
        return sprintf($format, $schema, $host, $url);
    }
}
