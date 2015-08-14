<?php

namespace Smt\FixtureGenerator\Generator;

/**
 * Data generator interface
 * @package Smt\FixtureGenerator\Generator
 * @author Kirill Saksin <kirillsaksin@yandex.ru>
 */
interface Generator
{
    /**
     * Generate data
     * @param int $count Count of data instances
     * @return string
     */
    public function generate($count);
}
