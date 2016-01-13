<?php
namespace Synga\InheritanceFinder\Cache;

use Symfony\Component\Finder\Finder;
use Synga\InheritanceFinder\Parser\PhpClassParser;
use Synga\InheritanceFinder\PhpClass;

class IncrementalCacheBuilder implements IncrementalCacheBuilderInterface
{
    /**
     * Finder object which is needed to find all php files
     *
     * @var Finder
     */
    private $finder;

    /**
     * Php parse which fills a PhpClass object
     *
     * @var PhpClassParser
     */
    private $phpClassParser;

    /**
     * IncrementalCacheBuilder constructor.
     * @param Finder $finder
     * @param PhpClassParser $phpClassParser
     */
    public function __construct(Finder $finder, PhpClassParser $phpClassParser) {
        $this->finder         = $finder;
        $this->phpClassParser = $phpClassParser;
    }

    /**
     * Builds the incremental cache
     *
     * @param $directory
     * @param \Synga\InheritanceFinder\PhpClass[] $phpClasses
     * @param PhpClass $phpClassClone
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    public function build($directory, $phpClasses, PhpClass $phpClassClone) {
        $time          = microtime(true);
        $pathnameArray = $this->generatePathnameArray($phpClasses);

        $resultPhpClasses = $this->removeNonExistentClasses($pathnameArray, $phpClasses);

        $resultPhpClasses = $this->addNewClasses($directory, $pathnameArray, $resultPhpClasses, $phpClassClone);

        $resultPhpClasses = $this->modifyModifiedClasses($resultPhpClasses);

        return $resultPhpClasses;
    }

    /**
     * Generates an array with pathname as key and the PhpClass as value
     *
     * @param PhpClass[] $phpClasses
     * @return array
     */
    protected function generatePathnameArray($phpClasses) {
        $pathnameArray = [];

        foreach ($phpClasses as $phpClass) {
            $pathnameArray[$phpClass->getFile()->getPathname()] = $phpClass;
        }

        return $pathnameArray;
    }


    /**
     * Removes files which are no longer present in this directory
     *
     * @param PhpClass[] $pathnameArray
     * @param PhpClass[] $phpClasses
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    protected function removeNonExistentClasses(&$pathnameArray, $phpClasses) {
        foreach ($pathnameArray as $pathname => $phpClass) {
            if (!file_exists($pathname)) {
                unset($phpClasses[$phpClass->getFullQualifiedNamespace()]);
                unset($pathnameArray[$pathname]);
            }
        }

        return $phpClasses;
    }

    /**
     * Checks if a file is modified and if so, reparse the file
     *
     * @param phpClass[] $phpClasses
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    protected function modifyModifiedClasses($phpClasses) {
        foreach ($phpClasses as $fullQualifiedNamespace => $phpClass) {
            if (file_exists($phpClass->getFile()->getPathname()) && $phpClass->getLastModified()->getTimestamp() != $phpClass->getFile()->getMTime()) {
                $file = $phpClass->getFile();

                $phpClass->clear();

                $result = $this->phpClassParser->parse($phpClass, $file);
                if ($result === false) {
                    unset($phpClasses[$fullQualifiedNamespace]);
                }
            }
        }

        return $phpClasses;
    }

    /**
     * Finds classes which are not added to the cache
     *
     * @param $directory
     * @param $phpClasses
     */
    protected function addNewClasses($directory, &$pathnameArray, $phpClasses, PhpClass $phpClassClone) {
        $files = $this->finder->files()->name('*.php')->contains('class')->contains('trait')->contains('interface')->in($directory);
        $i     = 0;
        $ii    = 0;

        foreach ($files as $file) {
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            $pathname = $file->getPathname();
            if (!isset($pathnameArray[$pathname])) {
                $i++;
                $phpClass = clone $phpClassClone;
                $result   = $this->phpClassParser->parse($phpClass, $file);

                if ($result !== false) {
                    $ii++;
                    $phpClasses[$phpClass->getFullQualifiedNamespace()] = $phpClass;
                    $pathnameArray[$pathname]                           = $phpClass;
                }
            }
        }

        return $phpClasses;
    }
}