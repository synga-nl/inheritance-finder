<?php
namespace Synga\InheritanceFinder\Parser;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Symfony\Component\Finder\SplFileInfo;
use Synga\InheritanceFinder\Parser\Visitors\NodeVisitorInterface;
use Synga\InheritanceFinder\PhpClass;

class PhpClassParser
{
    /**
     * The parser which will parse all the php files so we can determine if a php file contains a class, interface or
     * trait
     *
     * @var \PhpParser\Parser
     */
    protected $phpParser;

    /**
     * @var NodeVisitorInterface
     */
    private $nodeVisitor;

    /**
     * PhpClassParser constructor.
     * @param Parser $phpParser
     * @param NodeVisitorInterface $nodeVisitor
     */
    public function __construct(Parser $phpParser, NodeVisitorInterface $nodeVisitor) {
        $this->phpParser = $phpParser;
        $this->nodeVisitor = $nodeVisitor;
    }

    /**
     * Builds the cache
     *
     * @param PhpClass $phpClass
     * @param SplFileInfo $fileInfo
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    public function parse(PhpClass $phpClass, SplFileInfo $fileInfo) {
        try {
            $this->nodeVisitor->setPhpClass($phpClass);

            $this->traverse($fileInfo->getContents(), [$this->nodeVisitor]);

            $phpClass->setFile($fileInfo);

            $phpClass->setLastModified((new \DateTime())->setTimestamp($fileInfo->getMTime()));
        } catch (\Exception $e) {
            return false;
        }

        if($phpClass->isValid()){
            return $phpClass;
        }

        return false;
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

}