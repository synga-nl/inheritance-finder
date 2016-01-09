<?php
namespace Synga\InheritanceFinder;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Synga\InheritanceFinder\Parser\Visitors\ClassVisitor;

/**
 * Can build a cache of all the classes in a certain directory (usually your whole project) and can find which classes
 * extend or implement a class, or uses a certain trait.
 *
 * Class PhpFileFinder
 * @package Synga\InheritanceFinder
 *
 * @todo Move building the cache apart from the finder.
 */
class PhpFileFinder
{
    use NamespaceHelper;

    /**
     * Finder object which is needed to find all php files
     *
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * The parser which will parse all the php files so we can determine if a php file contains a class, interface or
     * trait
     *
     * @var \PhpParser\Parser
     */
    protected $phpParser;

    /**
     * The classes we already checked
     *
     * @var string[]
     */
    protected $classes;

    /**
     * The php files which need to be serialized and stored in the cache file
     *
     * @var PhpClass[]
     */
    protected $phpFiles;

    /**
     * The path to the cache file
     * @todo make an caching interface so the user can choose which caching method he wants to use
     *
     * @var string
     */
    protected $cachePath;

    /**
     * An array filled with objects of PhpClass fetched from the cache
     *
     * @var PhpClass[]
     */
    protected $cache;

    /**
     * PhpFileFinder constructor.
     * @param $cachePath
     * @param \Symfony\Component\Finder\Finder $finder
     * @param Parser $phpParser
     */
    public function __construct($cachePath, \Symfony\Component\Finder\Finder $finder, Parser $phpParser) {
        $this->cachePath = $cachePath;
        $this->finder    = $finder;
        $this->phpParser = $phpParser;
    }

    /**
     * Returns the built cache, or fetches the cache and then returns it
     *
     * @return PhpClass[]
     */
    protected function getCache() {
        if (empty($this->cache)) {
            $this->cache = unserialize(file_get_contents($this->cachePath));
        }

        return $this->cache;
    }

    /**
     * Finds the given class
     *
     * @param $fullQualifiedNamespace
     * @return PhpClass
     */
    public function findClass($fullQualifiedNamespace) {
        $fullQualifiedNamespace = ltrim($fullQualifiedNamespace, '\\');

        foreach ($this->getCache() as $phpClass) {
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
     * @return PhpClass[]
     */
    public function findExtends($fullQualifiedNamespace) {
        $type = 'extends';
        $fullQualifiedNamespace = ltrim($fullQualifiedNamespace, '\\');

        $phpClasses = [];

        foreach ($this->getCache() as $phpClass) {
            if ($fullQualifiedNamespace === $phpClass->getExtends()) {
                $phpClasses[] = $phpClass;
                $phpClasses   = array_merge($phpClasses, $this->findExtends($phpClass->getFullQualifiedNamespace()));
            }
        }

        return $this->arrayUniqueObject($phpClasses);
    }

    /**
     * Finds classes which implements the given interface
     *
     * @param $fullQualifiedNamespace
     * @return PhpClass[]
     */
    public function findImplements($fullQualifiedNamespace) {
        return $this->findImplementsOrTraitUse($fullQualifiedNamespace, 'implements');
    }

    /**
     * Finds classes which uses the given trait
     *
     * @param $fullQualifiedNamespace
     * @return array
     */
    public function findTraitUse($fullQualifiedNamespace) {
        return $this->findImplementsOrTraitUse($fullQualifiedNamespace, 'traits');
    }

    /**
     * Can find implements or detect trait use
     *
     * @param $fullQualifiedNamespace
     * @param $type
     * @return array
     */
    protected function findImplementsOrTraitUse($fullQualifiedNamespace, $type){
        $fullQualifiedNamespace = ltrim($fullQualifiedNamespace, '\\');

        $phpClasses = [];

        foreach ($this->getCache() as $phpClass) {
            $implementsOrTrait = $phpClass->{'get'.ucfirst($type)}();
            if (is_array($implementsOrTrait) && in_array($fullQualifiedNamespace, $implementsOrTrait)) {
                $phpClasses[] = $phpClass;
                $phpClasses   = array_merge($phpClasses, $this->findExtends($phpClass->getFullQualifiedNamespace()));
            }
        }

        return $this->arrayUniqueObject($phpClasses);
    }

    protected function find($fullQualifiedNamespace, $method){
        $fullQualifiedNamespace = ltrim($fullQualifiedNamespace, '\\');

        $phpClasses = [];

        foreach ($this->getCache() as $phpClass) {
            $usesTraits = $phpClass->getTraits();
            if (is_array($usesTraits) && in_array($fullQualifiedNamespace, $usesTraits)) {
                $phpClasses[] = $phpClass;
                $phpClasses   = array_merge($phpClasses, $this->findExtends($phpClass->getFullQualifiedNamespace()));
            }
        }

        return $this->arrayUniqueObject($phpClasses);
    }

    /**
     * Builds the cache
     *
     * @param $searchDirectory
     */
    public function buildCache($searchDirectory) {
        $this->isFirstCall();

        $classVisitor = new ClassVisitor();

        $i = 0;
        foreach ($this->findFiles($searchDirectory) as $file) {
            try {
                $phpClass = new PhpClass();

                $classVisitor->setPhpClass($phpClass);

                $this->traverse($file->getContents(), [$classVisitor]);

                $phpClass->setFile($file);

                $this->phpFiles[] = $phpClass;
            } catch (\Exception $e) {

            }
            $i++;
            echo $i . "\r\n";
        }

        $phpFiles = $this->returnPhpFiles();

        file_put_contents($this->cachePath, serialize($phpFiles));
    }

    /**
     * Find files based on some criteria
     *
     * @param $directory
     * @return \Symfony\Component\Finder\Finder|\Symfony\Component\Finder\SplFileInfo[]
     */
    protected function findFiles($directory) {
        $finder = $this->finder->create();
        $finder->files()->name('*.php');

        return $finder->in($directory);
    }

    /**
     * Traverse the parser nodes so we can extract the information
     *
     * @param $content
     * @param array $nodeVisitors
     * @internal param bool $addInterface
     */
    protected function traverse($content, array $nodeVisitors = []) {
        $nodes = $this->phpParser->parse($content);

        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NameResolver());

        foreach ($nodeVisitors as $nodeVisitor) {
            $traverser->addVisitor($nodeVisitor);
        }

        $traverser->traverse($nodes);
    }

    /**
     * Checks if current call is first by detecting if $this->classes and $this->phpFiles is empty. When it isn't it is
     * the first call and we fill up the variables with an empty array
     *
     * @return bool
     */
    protected function isFirstCall() {
        if ($this->classes === null && $this->phpFiles === null) {
            $this->classes  = [];
            $this->phpFiles = [];

            return true;
        }

        return false;
    }

    /**
     * It returns $this->phpFiles and sets $this->classes and $this->phpFiles to null so we know next time it is the
     * first call
     *
     * @return PhpClass[]
     */
    protected function returnPhpFiles() {
        $phpFiles       = $this->phpFiles;
        $this->classes  = null;
        $this->phpFiles = null;

        return $phpFiles;
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