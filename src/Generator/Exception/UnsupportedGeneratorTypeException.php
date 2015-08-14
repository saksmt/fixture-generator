<?php

namespace Smt\FixtureGenerator\Generator\Exception;

/**
 * Thrown when trying to register generator that doesn't implement @link Generator interface
 * @package Smt\FixtureGenerator\Generator\Exception
 * @author Kirill Saksin <kirillsaksin@yandex.ru>
 */
class UnsupportedGeneratorTypeException extends \Exception
{
}
