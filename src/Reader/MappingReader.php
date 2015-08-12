<?php

namespace Smt\FixtureGenerator\Reader;

use Smt\FixtureGenerator\Reader\Exception\UnreadableFileException;

/**
 * Reads mappings from files
 * @package Smt\FixtureGenerator\Reader
 * @author Kirill Saksin <kirill.saksin@yandex.ru>
 */
class MappingReader
{
    /**
     * @var string[] Paths to files
     */
    private $files;

    /**
     * @var array Mappings
     */
    private $mappings;

    /**
     * Constructor
     * @param string[] $files File paths
     */
    public function __construct(array $files)
    {
        $this->files = $files;
    }

    /**
     * Read mappings from files
     * @return MappingReader This instance
     * @throws UnreadableFileException
     */
    public function read()
    {
        foreach ($this->files as $file) {
            if (!is_readable($file)) {
                throw new UnreadableFileException($file);
            }
            $this->load($file);
        }
        return $this;
    }

    /**
     * Get mappings
     * @return array Mappings indexed by class names
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * Load mapping
     * @param string $file Path to mapping file
     */
    private function load($file)
    {
        $mapping = json_decode(file_get_contents($file), true);
        $this->mappings[$mapping['class']] = $mapping;
    }
}
