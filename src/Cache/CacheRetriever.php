<?php
/**
 * Created by PhpStorm.
 * User: roy
 * Date: 09-01-16
 * Time: 23:16
 */

namespace Synga\InheritanceFinder\Cache;


use Synga\InheritanceFinder\Cache\Strategy\CacheStrategyInterface;
use Synga\InheritanceFinder\PhpClass;

/**
 * Retrieves the cache
 *
 * @todo Maybe its better to generate a md5 of the file path and the full qualified namespace. If this package is used in a global setup than this could go terribly wrong:)
 *
 * Class CacheRetriever
 * @package Synga\InheritanceFinder\Cache
 */
class CacheRetriever
{
    /**
     * @var CacheBuilder
     */
    private $cacheBuilder;

    /**
     * @var CacheStrategyInterface
     */
    private $cacheStrategy;

    /**
     * Empty PhpClass which is cloned for the found classes in the builder.
     *
     * @var PhpClass
     */
    private $phpClass;

    /**
     * Contains the full qualified namespaces for every directory
     *
     * @var array
     */
    private $classes;

    /**
     * Contains all unique classes which will be matched against $this->classes. This reduces memory because a large
     * project can contain thousands of PhpClasses which can result in massive memory usage.
     *
     * @var PhpClass[]
     */
    private $phpClasses;

    /**
     * CacheRetriever constructor.
     *
     * @param CacheStrategyInterface $cacheStrategy
     * @param CacheBuilder $cacheBuilder
     * @param PhpClass $phpClass
     */
    public function __construct(CacheStrategyInterface $cacheStrategy, CacheBuilder $cacheBuilder, PhpClass $phpClass) {
        $this->cacheBuilder  = $cacheBuilder;
        $this->phpClass      = $phpClass;
        $this->cacheStrategy = $cacheStrategy;
    }

    /**
     * Retrieve the PhpFile objects from the cache or build them when they expired
     *
     * @param $directory
     * @return array|\Synga\InheritanceFinder\PhpClass[]
     */
    public function retrieve($directory) {
        $cache = [];

        $localCache = $this->getLocalCache($directory);

        if ($localCache === false) {
            $directory = realpath($directory);
            if (is_dir($directory)) {
                $retrievedCache = $this->cacheStrategy->get($directory);
                if ($retrievedCache === false) {
                    $cache = $this->cacheBuilder->build($directory, $this->phpClass);

                    $this->generateLocalCache($directory, $cache);

                    $this->cacheStrategy->set($directory, $cache, 24 * 3600);
                } else {
                    $cache = $retrievedCache;
                }
            }
        } else {
            $cache = $localCache;
        }

        return $cache;
    }

    /**
     * Caches the PhpFiles and the classes
     *
     * @param $directory
     * @param PhpClass[] $phpClasses
     */
    protected function generateLocalCache($directory, $phpClasses) {
        if (!isset($this->classes[$directory])) {
            $this->classes[$directory] = [];
        } else {
            return;
        }

        foreach ($phpClasses as $phpClass) {
            $fullQualifiedNamespace    = $phpClass->getFullQualifiedNamespace();
            $this->classes[$directory][] = $fullQualifiedNamespace;
            if (!isset($this->phpClasses[$fullQualifiedNamespace])) {
                $this->phpClasses[$fullQualifiedNamespace] = $phpClass;
            }
        }
    }

    /**
     * Matches $this->classes to $this->phpClasses so we use as less as memory
     *
     * @param $directory
     * @return array|bool
     */
    public function getLocalCache($directory) {
        $cache = [];

        if (isset($this->classes[$directory]) && is_array($this->classes[$directory])) {
            foreach ($this->classes[$directory] as $class) {
                if (isset($this->phpClasses[$class])) {
                    $cache[] = $this->phpClasses[$class];
                }
            }
        }

        if (empty($cache)) {
            return false;
        }

        return $cache;
    }
}