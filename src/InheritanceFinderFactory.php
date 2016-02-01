<?php
namespace Synga\InheritanceFinder;

use PhpParser\ParserFactory;
use Synga\InheritanceFinder\Database\DatabaseConfig;
use Synga\InheritanceFinder\File\FileConfig;
use Synga\InheritanceFinder\Parser\PhpClassParser;
use Synga\InheritanceFinder\Parser\Visitors\ClassNodeVisitor;

/**
 * Class InheritanceFinderFactory
 * @package Synga\InheritanceFinder
 */
class InheritanceFinderFactory
{
    /**
     * Makes an InheritanceFinder
     *
     * @param ConfigInterface $config
     * @return InheritanceFinder
     */
    public static function getInheritanceFinder(ConfigInterface $config) {
        if($config instanceof FileConfig){
            $cacheStrategy = new \Synga\InheritanceFinder\File\CacheStrategy($config);
        } elseif($config instanceof DatabaseConfig){
            $cacheStrategy = new \Synga\InheritanceFinder\Database\CacheStrategy($config);
        }

        return new \Synga\InheritanceFinder\InheritanceFinder(
            new \Synga\InheritanceFinder\CacheBuilder(
                $cacheStrategy,
                new PhpClassParser((new ParserFactory)->create(ParserFactory::PREFER_PHP7), new ClassNodeVisitor()),
                new \Symfony\Component\Finder\Finder(),
                new \Synga\InheritanceFinder\Helpers\FastArrayAccessHelper()
            )
        );
    }
}