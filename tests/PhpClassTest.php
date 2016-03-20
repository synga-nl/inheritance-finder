<?php

/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */
class PhpClassTest extends TestCase
{
    public function testSerialization() {
        $fullQualifiedNamespace = 'Full\Qualified\Namespace';

        $phpClass = new \Synga\InheritanceFinder\PhpClass();
        $file     = new \Symfony\Component\Finder\SplFileInfo(__FILE__, __FILE__, __FILE__);
        $phpClass->setFile($file);
        $phpClass->setFullQualifiedNamespace($fullQualifiedNamespace);

        $serialized = serialize($phpClass);

        $phpClass2 = unserialize($serialized);

        $this->assertSame($fullQualifiedNamespace, $phpClass2->getFullQualifiedNamespace());
    }

    public function testFullQualifiedNamespaceToClassAndNamespace() {
        $fullQualifiedNamespace = 'Full\Qualified\Namespace';
        $class     = 'Namespace';
        $namespace = 'Full\Qualified';

        $phpClass = new \Synga\InheritanceFinder\PhpClass();
        $phpClass->setFullQualifiedNamespace($fullQualifiedNamespace);

        $this->assertSame($namespace, $phpClass->getNamespace());
        $this->assertSame($class, $phpClass->getClass());
    }

    public function testClassAndNamespaceToFullQualifiedNamespace() {
        $fullQualifiedNamespace = 'Full\Qualified\Namespace';
        $class     = 'Namespace';
        $namespace = 'Full\Qualified';

        $phpClass = new \Synga\InheritanceFinder\PhpClass();
        $phpClass->setClass($class);
        $phpClass->setNamespace($namespace);

        $this->assertSame($fullQualifiedNamespace, $phpClass->getFullQualifiedNamespace());
    }

    public function testIsValid(){
        $fullQualifiedNamespace = 'Full\Qualified\Namespace';
        $phpClass = new \Synga\InheritanceFinder\PhpClass();

        $phpClass->setFullQualifiedNamespace($fullQualifiedNamespace);
        $this->assertTrue($phpClass->isValid());
    }

    public function testIsNotValid(){
        $phpClass = new \Synga\InheritanceFinder\PhpClass();
        $this->assertFalse($phpClass->isValid());
    }
}