<?php
namespace Synga\InheritanceFinder\Cache;

use Synga\InheritanceFinder\PhpClass;

/**
 * Interface CacheBuilderInterface
 * @package Synga\InheritanceFinder\Cache
 */
interface CacheBuilderInterface
{
    /**
     * Builds the cache
     *
     * @param $searchDirectory
     * @param PhpClass $phpClassClone
     */
    public function build($searchDirectory, PhpClass $phpClassClone);
}