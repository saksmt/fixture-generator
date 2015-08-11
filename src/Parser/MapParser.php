<?php

namespace Smt\FixtureGenerator\Parser;

/**
 *
 * @package Smt\FixtureGenerator\Parser
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class MapParser
{
    public function parse($data)
    {
        $flat = json_decode($data, true);
        $classMaps = [];
        foreach ($flat as $flatClass) {
            $classMaps[] = new ClassMap($flatClass);
        }
        return $classMaps;
    }
}
