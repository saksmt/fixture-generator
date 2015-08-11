<?php

namespace Smt\FixtureGenerator\Application;

use Symfony\Component\Console\Application;

/**
 *
 * @package Smt\FixtureGenerator\Application
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class FixtureGeneratorApp extends Application
{
    const VERSION = '0.0.0';

    public function __construct()
    {
        parent::__construct('smt/fixture-generator', self::VERSION);
        $this->add(new GenerateFixtureCommand());
    }
}
