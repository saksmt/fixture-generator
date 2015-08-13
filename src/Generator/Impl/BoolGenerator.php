<?php

namespace Smt\FixtureGenerator\Generator\Impl;

use Smt\FixtureGenerator\Generator\Generator;

/**
 * Generate boolean value
 * @package Smt\FixtureGenerator\Generator\Impl
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class BoolGenerator implements Generator
{
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
        return rand(false, true) ? 'true' : 'false';
    }
}
