<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder\Helpers;


/**
 * Class FastArrayAccessHelper
 * @package Synga\InheritanceFinder\Helpers
 */
class FastArrayAccessHelper
{
    /**
     * Can transform a normal array in a pathname array. This means that the key of the array is the path to the class
     * and the value is a PhpClass
     *
     * @param \Synga\InheritanceFinder\PhpClass[]
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    public function getPathnameArray($array){
        $result = [];

        foreach ($array as $item) {
            /* @var $item \Synga\InheritanceFinder\PhpClass */
            $result[$item->getFile()->getPathname()] = $item;
        }

        return $result;
    }
}