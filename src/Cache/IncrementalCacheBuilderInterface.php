<?php
namespace Synga\InheritanceFinder\Cache;

use Synga\InheritanceFinder\PhpClass;

interface IncrementalCacheBuilderInterface
{
    /**
     * Builds the incremental cache
     *
     * @param $directory
     * @param PhpClass[] $phpClasses
     * @param PhpClass $phpClassClone
     * @return mixed
     */
    public function build($directory, $phpClasses, PhpClass $phpClassClone);
}