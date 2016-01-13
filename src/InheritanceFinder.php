<?php
namespace Synga\InheritanceFinder;

use PhpParser\Parser;
use Synga\InheritanceFinder\Cache\CacheRetriever;

/**
 * Can build a cache of all the classes in a certain directory (usually your whole project) and can find which classes
 * extend or implement a class, or uses a certain trait.
 *
 * @todo make sure you can force a new cache.
 *
 * Class InheritanceFinder
 * @package Synga\InheritanceFinder
 */
class InheritanceFinder
{
    use NamespaceHelper;

    /**
     * @var CacheRetriever
     */
    private $cacheRetriever;

    /**
     * InheritanceFinder constructor.
     * @param CacheRetriever $cacheRetriever
     */
    public function __construct(CacheRetriever $cacheRetriever) {
        $this->cacheRetriever = $cacheRetriever;
    }

    /**
     * Finds the given class
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return PhpClass
     */
    public function findClass($fullQualifiedNamespace, $directory) {
        $fullQualifiedNamespace = ltrim($fullQualifiedNamespace, '\\');

        foreach ($this->cacheRetriever->retrieve($directory) as $phpClass) {
            if ($fullQualifiedNamespace === $phpClass->getFullQualifiedNamespace()) {
                return $phpClass;
            }
        }

        return false;
    }

    /**
     * Finds classes which extend the given class
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return PhpClass[]
     */
    public function findExtends($fullQualifiedNamespace, $directory) {
        $fullQualifiedNamespace = ltrim($fullQualifiedNamespace, '\\');

        $phpClasses = [];

        foreach ($this->cacheRetriever->retrieve($directory) as $phpClass) {
            if ($fullQualifiedNamespace === $phpClass->getExtends()) {
                $phpClasses[] = $phpClass;
                $phpClasses   = array_merge($phpClasses, $this->findExtends($phpClass->getFullQualifiedNamespace(), $directory));
            }
        }

        return $this->arrayUniqueObject($phpClasses);
    }

    /**
     * Finds classes which implements the given interface
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return PhpClass[]
     */
    public function findImplements($fullQualifiedNamespace, $directory) {
        return $this->findImplementsOrTraitUse($fullQualifiedNamespace, $directory, 'implements');
    }

    /**
     * Finds classes which uses the given trait
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return array
     */
    public function findTraitUse($fullQualifiedNamespace, $directory) {
        return $this->findImplementsOrTraitUse($fullQualifiedNamespace, $directory, 'traits');
    }

    /**
     * Gets the cache retriever
     *
     * @return CacheRetriever
     */
    public function getCacheRetriever() {
        return $this->cacheRetriever;
    }

    /**
     * Can find implements or detect trait use
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @param $type
     * @return array
     */
    protected function findImplementsOrTraitUse($fullQualifiedNamespace, $directory, $type) {
        $fullQualifiedNamespace = ltrim($fullQualifiedNamespace, '\\');

        $phpClasses = [];

        foreach ($this->cacheRetriever->retrieve($directory) as $phpClass) {
            $implementsOrTrait = $phpClass->{'get' . ucfirst($type)}();
            if (is_array($implementsOrTrait) && in_array($fullQualifiedNamespace, $implementsOrTrait)) {
                $phpClasses[] = $phpClass;
                $phpClasses   = array_merge($phpClasses, $this->findExtends($phpClass->getFullQualifiedNamespace(), $directory));
            }
        }

        return $this->arrayUniqueObject($phpClasses);
    }

    /**
     * Makes an array of object unique using the spl_object_hash function
     *
     * @param $phpClasses
     * @return array
     */
    protected function arrayUniqueObject($phpClasses) {
        $hashes = [];

        foreach ($phpClasses as $key => $phpClass) {
            $hashes[$key] = spl_object_hash($phpClass);
        }

        $hashes = array_unique($hashes);

        $uniqueArray = [];

        foreach ($hashes as $key => $hash) {
            $uniqueArray[$key] = $phpClasses[$key];
        }

        return $uniqueArray;
    }
}