<?php
namespace Synga\InheritanceFinder;

use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Synga\InheritanceFinder\Cache\CacheBuilder;
use Synga\InheritanceFinder\Cache\CacheRetriever;
use Synga\InheritanceFinder\Cache\Strategy\FileCacheStrategy;
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
     * @var CacheRetriever
     */
    private static $cacheRetriever;

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

        $cacheDirectory = realpath($cacheDirectory);

        if (empty(self::$cacheRetriever[$cacheDirectory])) {
            self::$cacheRetriever[$cacheDirectory] = new CacheRetriever(
                new FileCacheStrategy($cacheDirectory),
                new CacheBuilder(
                    new ClassNodeVisitor(),
                    new Finder(),
                    (new ParserFactory)->create(ParserFactory::PREFER_PHP7)
                ),
                self::$phpClass
            );
        }

        return new InheritanceFinder(
            self::$cacheRetriever
        );
    }
}