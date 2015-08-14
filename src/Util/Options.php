<?php

namespace Smt\FixtureGenerator\Util;

/**
 *
 * @package Smt\FixtureGenerator\Util
 * @author Kirill Saksin <kirillsaksin@yandex.ru>
 */
class Options
{
    /**
     * Merge options
     * @param array $options1 Default options
     * @param array $options2 User options
     * @return array Result options set
     */
    public static function merge(array $options1, array $options2)
    {
        $result = [];
        foreach ($options1 as $key => $option) {
            if (isset($options2[$key])) {
                if (is_array($option)) {
                    $result[$key] = self::merge($option, $options2[$key]);
                } else {
                    $result[$key] = $options2[$key];
                }
                unset($options2[$key]);
            } else {
                $result[$key] = $option;
            }
        }
        return array_merge($result, $options2);
    }
}
