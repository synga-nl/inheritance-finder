<?php
namespace Synga\InheritanceFinder\Cache;

use PhpParser\Parser;
use Symfony\Component\Finder\Finder;
use Synga\InheritanceFinder\Parser\PhpClassParser;
use Synga\InheritanceFinder\PhpClass;

/**
 * Can walk over all the files in a directory recursively. All these files are parsed and it will extract information
 * about classes, interfaces and trait from the parsed nodes.
 *
 * @todo add building the cache based on the change date so we don't have to parse all files but only the modified and added ones.
 *
 * Class CacheBuilder
 * @package Synga\InheritanceFinder\Cache
 */
class CacheBuilder implements CacheBuilderInterface
{
    /**
     * Finder object which is needed to find all php files
     *
     * @var Finder
     */
    protected $finder;

    /**
     * The classes we already checked
     *
     * @var string[]
     */
    protected $classes;

    /**
     * The php files which need to be serialized and stored in the cache file
     *
     * @var PhpClass[]
     */
    protected $phpFiles;

    /**
     * @var PhpClassParser
     */
    private $phpClassParser;

    /**
     * CacheBuilder constructor.
     * @param Finder $finder
     * @param PhpClassParser $phpClassParser
     */
    public function __construct(Finder $finder, PhpClassParser $phpClassParser) {
        $this->finder         = $finder;
        $this->phpClassParser = $phpClassParser;
    }

    /**
     * Builds the cache
     *
     * @param $searchDirectory
     * @param PhpClass $phpClassClone
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    public function build($searchDirectory, PhpClass $phpClassClone) {
        $this->isFirstCall();

        foreach ($this->findFiles($searchDirectory) as $file) {
            $phpClass = clone $phpClassClone;
            $result   = $this->phpClassParser->parse($phpClass, $file);

            if ($result !== false) {
                $this->phpFiles[$phpClass->getFullQualifiedNamespace()] = $phpClass;
            }
        }

        return $this->returnPhpFiles();
    }

    /**
     * Checks if current call is first by detecting if $this->classes and $this->phpFiles is empty. When these are empty
     * this is the first call and we fill up the variables with an empty array
     *
     * @return bool
     */
    protected function isFirstCall() {
        if ($this->classes === null && $this->phpFiles === null) {
            $this->classes  = [];
            $this->phpFiles = [];

            return true;
        }

        return false;
    }

    /**
     * It returns $this->phpFiles and sets $this->classes and $this->phpFiles to null so we know next time it is the
     * first call
     *
     * @return PhpClass[]
     */
    protected function returnPhpFiles() {
        $phpFiles       = $this->phpFiles;
        $this->classes  = null;
        $this->phpFiles = null;

        return $phpFiles;
    }


    /**
     * Find files based on some criteria
     *
     * @param $directory
     * @return Finder|\Symfony\Component\Finder\SplFileInfo[]
     */
    protected function findFiles($directory) {
        $finder = $this->finder->create();
        $finder->files()->name('*.php');

        return $finder->in($directory);
    }
}