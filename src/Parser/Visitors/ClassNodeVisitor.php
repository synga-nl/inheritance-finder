<?php
namespace Synga\InheritanceFinder\Parser\Visitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeVisitorAbstract;
use Synga\InheritanceFinder\PhpClass;

/**
 * This class visits every node from the parser and extracts information and sets it on the PhpClass object.
 *
 * Class ClassNodeVisitor
 * @package Synga\InheritanceFinder\Parser\Visitors
 */
class ClassNodeVisitor extends NodeVisitorAbstract implements NodeVisitorInterface
{
    /**
     * The PhpClass object to fill with data from the parser
     *
     * @var \Synga\InheritanceFinder\PhpClass
     */
    protected $phpClass;

    /**
     * Setter for the PhpClass object to fill with data from the parser
     *
     * @param PhpClass $phpClass
     * @return void
     */
    public function setPhpClass(PhpClass $phpClass) {
        $this->phpClass = $phpClass;
    }

    /**
     * This code can evaluate the data from the parser. It sorts out which object type we are dealing with and passes
     * this to the PhpClass
     *
     * @param Node $node
     * @return false|null|Node|\PhpParser\Node[]|void
     */
    public function leaveNode(Node $node) {
        if ($this->phpClass instanceof PhpClass) {
            if ($node instanceof Class_) {
                if (!empty($node->extends) && count($node->extends->parts) > 0) {
                    $this->phpClass->setExtends(implode('\\', $node->extends->parts));
                }

                if (!empty($node->implements) && is_array($node->implements)) {
                    foreach ($node->implements as $implements) {
                        $this->phpClass->setImplements(implode('\\', $implements->parts));
                    }
                }

                if ($node->type === 0) {
                    $this->phpClass->setClassType(PhpClass::TYPE_CLASS);
                } elseif ($node->type === 16) {
                    $this->phpClass->setClassType(PhpClass::TYPE_ABSTRACT_CLASS);
                } elseif ($node->type === 32) {
                    $this->phpClass->setClassType(PhpClass::TYPE_FINAL_CLASS);
                }

                $this->phpClass->setClass($node->name);
            } elseif ($node instanceof Interface_) {
                if (!empty($node->extends) && is_array($node->extends)) {
                    foreach ($node->extends as $implements) {
                        $this->phpClass->setImplements(implode('\\', $implements->parts));
                    }
                }

                $this->phpClass->setClassType(PhpClass::TYPE_INTERFACE);
                $this->phpClass->setClass($node->name);
            } elseif ($node instanceof Trait_) {
                $this->phpClass->setClassType(PhpClass::TYPE_TRAIT);
                $this->phpClass->setClass($node->name);
            } elseif ($node instanceof Namespace_) {
                $this->phpClass->setNamespace(implode('\\', $node->name->parts));
            } elseif ($node instanceof TraitUse) {
                if (!empty($node->traits) && is_array($node->traits)) {
                    foreach ($node->traits as $trait) {
                        $this->phpClass->setTraits(implode('\\', $trait->parts));
                    }
                }
            }
        }
    }
}