<?php
namespace Synga\InheritanceFinder;

interface InheritanceFinderInterface
{
    /**
     * Finds the given class
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return PhpClass
     */
    public function findClass($fullQualifiedNamespace, $directory);

    /**
     * Finds classes which extend the given class
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return PhpClass[]
     */
    public function findExtends($fullQualifiedNamespace, $directory);

    /**
     * Finds classes which implements the given interface
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return PhpClass[]
     */
    public function findImplements($fullQualifiedNamespace, $directory);

    /**
     * Finds classes which uses the given trait
     *
     * @param $fullQualifiedNamespace
     * @param $directory
     * @return array
     */
    public function findTraitUse($fullQualifiedNamespace, $directory);

    /**
     * Gets the cache retriever
     *
     * @return CacheRetriever
     */
    public function getCacheRetriever();
}