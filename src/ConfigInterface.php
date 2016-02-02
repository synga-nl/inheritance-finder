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
 * Interface ConfigInterface
 * @package Synga\InheritanceFinder
 */
interface ConfigInterface
{
    /**
     * Gets the application root of this application
     *
     * @return mixed
     */
    public function getApplicationRoot();

    /**
     * Sets the application root of this application
     *
     * @param $applicationRoot
     * @return mixed
     */
    public function setApplicationRoot($applicationRoot);
}