<?php
namespace Synga\InheritanceFinder\Cache;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Symfony\Component\Finder\Finder;
use Synga\InheritanceFinder\Parser\Visitors\NodeVisitorInterface;
use Synga\InheritanceFinder\PhpClass;

/**
 * Can walk over all the files in a directory recursively. All these files are parsed and it will extract information
 * about classes, interfaces and trait from the parsed nodes.
 *
 * @todo add building the cache based on the change date so we don't have to parse all files but only the modified and added ones.
 *
 * Class CacheBuilder
 * @package Synga\InheritanceFinder\Cache
 */
class CacheBuilder implements CacheBuilderInterface
{
    /**
     * @var NodeVisitorInterface
     */
    private $nodeVisitor;

    /**
     * Finder object which is needed to find all php files
     *
     * @var Finder
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
     * CacheBuilder constructor.
     * @param NodeVisitorInterface $nodeVisitor
     * @param Finder $finder
     * @param Parser $phpParser
     */
    public function __construct(NodeVisitorInterface $nodeVisitor, Finder $finder, Parser $phpParser){
        $this->nodeVisitor = $nodeVisitor;
        $this->finder = $finder;
        $this->phpParser = $phpParser;
    }

    /**
     * Builds the cache
     *
     * @param $searchDirectory
     * @param PhpClass $phpClassClone
     * @param int $expire
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    public function build($searchDirectory, PhpClass $phpClassClone, $expire = -1) {
        $this->isFirstCall();

        foreach ($this->findFiles($searchDirectory) as $file) {
            try {
                $phpClass = clone $phpClassClone;

                $this->nodeVisitor->setPhpClass($phpClass);

                $this->traverse($file->getContents(), [$this->nodeVisitor]);

                $phpClass->setFile($file);

                $phpClass->setLastModified((new \DateTime())->setTimestamp($file->getMTime()));

                $this->phpFiles[$phpClass->getFullQualifiedNamespace()] = $phpClass;
            } catch (\Exception $e) {

            }
        }

        return $this->returnPhpFiles();
    }

    /**
     * Checks if current call is first by detecting if $this->classes and $this->phpFiles is empty. When these are empty
     * this is the first call and we fill up the variables with an empty array
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
     * Find files based on some criteria
     *
     * @param $directory
     * @return Finder|\Symfony\Component\Finder\SplFileInfo[]
     */
    protected function findFiles($directory) {
        $finder = $this->finder->create();
        $finder->files()->name('*.php');

        return $finder->in($directory);
    }
}