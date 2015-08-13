<?php

namespace Smt\FixtureGenerator\Command;

use Smt\Component\Console\Style\GentooStyle;
use Smt\FixtureGenerator\Generator\FixtureGenerator;
use Smt\FixtureGenerator\Reader\MappingReader;
use Smt\Streams\Exception\StreamNotWritableException;
use Smt\Streams\FileStream;
use Smt\Streams\SymfonyOutputStream;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @package Smt\FixtureGenerator\Command
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class GenerateFixtureCommand extends Command
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate fixtures file')
            ->addArgument('MAPPING_FILES', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to mapping file')
            ->addOption('output-file', 'o', InputOption::VALUE_REQUIRED, 'Path to generated fixtures, default to stdout')
            ->addOption('fixtures-count', 'c', InputOption::VALUE_REQUIRED, 'Fixtures to generate', 5)
            ->addOption('bootstrap', 'b', InputOption::VALUE_REQUIRED, 'Bootstrap file to load before class parsing')
        ;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable) $in
     */
    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $out = new GentooStyle($out, $in);
        $outStream = new SymfonyOutputStream($out);
        if ($in->hasOption('output-file') && !empty($in->getOption('output-file'))) {
            try {
                $outStream->redirect(FileStream::fromFilename($in->getOption('output-file')));
            } catch (StreamNotWritableException $e) {
                $out->error('File is not writable!');
                return;
            }
        }
        if ($in->hasOption('bootstrap')) {
            require_once $in->getOption('bootstrap');
        }
        $mappingFiles = $in->getArgument('MAPPING_FILES');
        $mappingReader = new MappingReader($mappingFiles);
        $mappingReader->read();
        $generator = new FixtureGenerator($mappingReader->getMappings());
        $outStream->write($generator->generate($in->getOption('fixtures-count')));
    }
}
