<?php

namespace Smt\FixtureGenerator\Application;

use Smt\FixtureGenerator\Command\GenerateFixtureCommand;
use Symfony\Component\Console\Application;

/**
 * Fixture generator application
 * @package Smt\FixtureGenerator\Application
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class FixtureGeneratorApp extends Application
{
    /**
     * @const string Version
     */
    const VERSION = '0.0.0';

    /** Constructor */
    public function __construct()
    {
        parent::__construct('smt/fixture-generator', self::VERSION);
        $this->add(new GenerateFixtureCommand());
    }
}
