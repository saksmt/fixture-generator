<?php

namespace Smt\FixtureGenerator\Generator\Exception;

/**
 * Thrown when trying to register generator that doesn't implement @link Generator interface
 * @package Smt\FixtureGenerator\Generator\Exception
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class UnsupportedGeneratorTypeException extends \Exception
{
}
