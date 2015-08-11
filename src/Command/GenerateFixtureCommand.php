<?php

namespace Smt\FixtureGenerator\Command;

use Smt\Component\Console\Style\GentooStyle;
use Smt\Streams\Exception\StreamNotWritableException;
use Smt\Streams\FileStream;
use Smt\Streams\SymfonyOutputStream;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @package Smt\FixtureGenerator\Command
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class GenerateFixtureCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate fixtures file')
            ->addArgument('MAPPING_FILE', InputArgument::REQUIRED, 'Path to mapping file')
            ->addArgument('OUTPUT_FILE', InputArgument::OPTIONAL, 'Path to generated fixtures, default to stdout')
        ;
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $out = new GentooStyle($out, $in);
        $outStream = new SymfonyOutputStream($out);
        if ($in->hasArgument('OUTPUT_FILE')) {
            try {
                $outStream->redirect(FileStream::fromFilename($in->getArgument('OUTPUT_FILE')));
            } catch (StreamNotWritableException $e) {
                $out->error('File is not writable!');
                return;
            }
        }
        $mapParser = new MapParser();
        $generator = new FixtureGenerator($mapParser->parse($in->getArgument('MAPPING_FILE')));
        $outStream->write($generator->generate());
    }
}
