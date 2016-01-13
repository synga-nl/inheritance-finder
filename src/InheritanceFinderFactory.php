<?php
namespace Synga\InheritanceFinder;

use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Synga\InheritanceFinder\Cache\CacheBuilder;
use Synga\InheritanceFinder\Cache\CacheRetriever;
use Synga\InheritanceFinder\Cache\IncrementalCacheBuilder;
use Synga\InheritanceFinder\Cache\Strategy\FileCacheStrategy;
use Synga\InheritanceFinder\Parser\PhpClassParser;
use Synga\InheritanceFinder\Parser\Visitors\ClassNodeVisitor;

/**
 * Class InheritanceFinderFactory
 * @package Synga\InheritanceFinder
 */
class InheritanceFinderFactory
{
    /**
     * @var PhpClass
     */
    private static $phpClass;

    /**
     * @var Finder
     */
    private static $finder;

    /**
     * @var CacheRetriever
     */
    private static $cacheRetriever;

    /**
     * @var
     */
    private static $phpClassParser;

    /**
     * Makes an InheritanceFinder
     *
     * @param $cacheDirectory
     * @return InheritanceFinder
     */
    public function getInheritanceFinder($cacheDirectory) {
        if (empty(self::$phpClass)) {
            self::$phpClass = new PhpClass();
        }

        if (empty(self::$finder)) {
            self::$finder = new Finder();
        }

        if (empty(self::$phpClassParser)) {
            self::$phpClassParser = new PhpClassParser((new ParserFactory)->create(ParserFactory::PREFER_PHP7), new ClassNodeVisitor());
        }

        $cacheDirectory = realpath($cacheDirectory);

        if (empty(self::$cacheRetriever[$cacheDirectory])) {
            self::$cacheRetriever[$cacheDirectory] = new CacheRetriever(
                new FileCacheStrategy($cacheDirectory),
                new CacheBuilder(
                    self::$finder,
                    self::$phpClassParser
                ),
                new IncrementalCacheBuilder(
                    self::$finder,
                    self::$phpClassParser
                ),
                self::$phpClass
            );
        }

        return new InheritanceFinder(
            self::$cacheRetriever[$cacheDirectory]
        );
    }
}