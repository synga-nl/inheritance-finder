<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder;


use Synga\InheritanceFinder\PhpClass;

interface InheritanceFinderInterface
{
    /**
     * Finds the given class
     *
     * @param $class
     * @return PhpClass
     */
    public function findClass($class);

    /**
     * Finds classes which extend the given class
     *
     * @param $class
     * @return PhpClass[]
     */
    public function findExtends($class);

    /**
     * Finds classes which implements the given interface
     *
     * @param $interface
     * @return PhpClass[]
     */
    public function findImplements($interface);

    /**
     * Finds classes which uses the given trait
     *
     * @param $trait
     * @return PhpClass[]
     */
    public function findTraitUse($trait);

    /**
     * @param $classes
     * @param $interfaces
     * @param $traits
     * @return bool|PhpClass[]
     */
    public function findMultiple($classes = [], $interfaces = [], $traits = []);
}