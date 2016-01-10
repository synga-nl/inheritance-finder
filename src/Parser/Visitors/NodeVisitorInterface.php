<?php
namespace Synga\InheritanceFinder\Parser\Visitors;

use PhpParser\NodeVisitor;
use Synga\InheritanceFinder\PhpClass;

/**
 * Interface NodeVisitorInterface
 * @package Synga\InheritanceFinder\Parser\Visitors
 */
interface NodeVisitorInterface extends NodeVisitor
{
    /**
     * Sets the PhpClass we want to fill with data from the PHP parser
     *
     * @param PhpClass $phpClass
     * @return mixed
     */
    public function setPhpClass(PhpClass $phpClass);
}