<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder\Helpers;


class FastArrayAccessHelper
{
    /**
     * @param \Synga\InheritanceFinder\PhpClass[]
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    public function getNamespaceArray($array) {
        $result = [];

        foreach ($array as $item) {
            /* @var $item \Synga\InheritanceFinder\PhpClass */
            $result[$item->getFullQualifiedNamespace()] = $item;
        }

        return $result;
    }

    /**
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