<?php

namespace Smt\FixtureGenerator\Map;

/**
 *
 * @package Smt\FixtureGenerator\Map
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class ClassMap
{
    private static $defaultData = [
        'class' => 'array',
        'fields' => [],
    ];

    public function __construct(array $mapping)
    {
        $data = array_merge_recursive($mapping, self::$defaultData);
        if ($data['class'] !== 'array') {
            self::loadClass()
        }
    }
}
