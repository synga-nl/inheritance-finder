<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder;


/**
 * Interface CacheBuilderInterface
 * @package Synga\InheritanceFinder
 */
interface CacheBuilderInterface
{
    /**
     * Gets the cache
     *
     * @return mixed
     */
    public function getCache();
}