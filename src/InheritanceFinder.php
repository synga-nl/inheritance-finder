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
 * Class InheritanceFinder
 * @package Synga\InheritanceFinder
 */
class InheritanceFinder implements InheritanceFinderInterface
{
    /**
     * @var CacheBuilderInterface
     */
    private $cacheBuilder;

    /**
     * @var PhpClass[]
     */
    private $localCache = [];

    /**
     * InheritanceFinder constructor.
     * @param CacheBuilderInterface $cacheBuilder
     */
    public function __construct(CacheBuilderInterface $cacheBuilder) {
        $this->cacheBuilder = $cacheBuilder;
    }

    /**
     * Finds a $class file
     *
     * @param $class
     * @return PhpClass
     */
    public function findClass($class) {
        $this->init();

        $fullQualifiedNamespace = $this->trimNamespace($class);

        foreach ($this->localCache as $phpClass) {
            if ($fullQualifiedNamespace === $phpClass->getFullQualifiedNamespace()) {
                return $phpClass;
            }
        }

        return false;
    }

    /**
     * Finds all classes which extend $class
     *
     * @param $class
     * @return PhpClass[]
     */
    public function findExtends($class) {
        $this->init();

        $fullQualifiedNamespace = $this->trimNamespace($class);

        $phpClasses = [];

        foreach ($this->localCache as $phpClass) {
            if ($fullQualifiedNamespace === $phpClass->getExtends()) {
                $phpClasses[] = $phpClass;
                $phpClasses   = array_merge($phpClasses, $this->findExtends($phpClass->getFullQualifiedNamespace()));
            }
        }

        return $this->arrayUniqueObject($phpClasses);
    }

    /**
     * Finds all classes which impelements $interface
     *
     * @param $interface
     * @return PhpClass[]
     */
    public function findImplements($interface) {
        $this->init();

        return $this->findImplementsOrTraitUse($interface, 'implements');
    }

    /**
     * Finds all classes which use $trait
     *
     * @param $trait
     * @return PhpClass[]
     */
    public function findTraitUse($trait) {
        $this->init();

        return $this->findImplementsOrTraitUse($trait, 'traits');
    }

    /**
     * Checks if $child is in the lineage of $parent
     *
     * @param $child
     * @param $parent
     * @return mixed
     */
    public function isSubclassOf($child, $parent) {
        try {
            return is_subclass_of($child, $parent);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Can find multiple classes at once.
     *
     * @param array $classes
     * @param array $interfaces
     * @param array $traits
     * @return PhpClass[]
     */
    public function findMultiple($classes = [], $interfaces = [], $traits = []) {
        $this->init();

        $classes    = $this->normalizeArray($classes);
        $interfaces = $this->normalizeArray($interfaces);
        $traits     = $this->normalizeArray($traits);

        $foundClasses = [];

        if ($classes !== false) {
            foreach ($classes as $class) {
                $foundClasses = array_merge($foundClasses, $this->findExtends($class));
            }
        }

        if ($interfaces !== false) {
            foreach ($interfaces as $interface) {
                $foundClasses = array_merge($foundClasses, $this->findImplements($interface));
            }
        }

        if ($traits !== false) {
            foreach ($traits as $trait) {
                $foundClasses = array_merge($foundClasses, $this->findTraitUse($trait));
            }
        }

        return $this->arrayUniqueObject($foundClasses);
    }

    /**
     * Can find implements or detect trait use
     *
     * @param $fullQualifiedNamespace
     * @param $type
     * @return PhpClass[]
     */
    protected function findImplementsOrTraitUse($fullQualifiedNamespace, $type) {
        $fullQualifiedNamespace = $this->trimNamespace($fullQualifiedNamespace);

        $phpClasses = [];

        $method = 'get' . ucfirst($type);

        foreach ($this->localCache as $phpClass) {
            $implementsOrTrait = $phpClass->$method();
            if (is_array($implementsOrTrait) && in_array($fullQualifiedNamespace, $implementsOrTrait)) {
                $phpClasses[] = $phpClass;
                $phpClasses   = array_merge($phpClasses, $this->findExtends($phpClass->getFullQualifiedNamespace()));
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

    /**
     * Chcecks if var $array is a string, if so, create an array with that string otherwise return the array. If we
     * couldn't create an array it returns false.
     *
     * @param $array
     * @return array|bool
     */
    protected function normalizeArray($array) {
        if (is_string($array)) {
            $array = [$array];
        }

        if (!is_array($array)) {
            return false;
        }

        return $array;
    }

    /**
     * Loads the cache so we can use it in all the "find" methods
     */
    protected function init() {
        if (empty($this->localCache)) {
            $this->localCache = $this->cacheBuilder->getCache();
        }
    }

    /**
     * Trims the first "\" which is copied default by PhpStorm (copy reference)
     *
     * @param $class
     * @return string
     */
    protected function trimNamespace($class) {
        return ltrim($class, '\\');
    }
}