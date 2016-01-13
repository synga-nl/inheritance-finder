<?php
namespace Synga\InheritanceFinder\Tests;

use Synga\InheritanceFinder\InheritanceFinder;
use Synga\InheritanceFinder\InheritanceFinderFactory;
use Synga\InheritanceFinder\PhpClass;

class InheritanceFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InheritanceFinder
     */
    protected $inheritanceFinder;

    protected static $cacheDirectory = __DIR__ . '/cache/';

    protected $projectRoot;

    protected function setUp() {
        if (!file_exists(self::$cacheDirectory)) {
            mkdir(self::$cacheDirectory);
        }

        $this->inheritanceFinder = (new InheritanceFinderFactory())->getInheritanceFinder(self::$cacheDirectory);
        $this->projectRoot       = realpath(__DIR__ . '/TestClasses/');
    }

    public static function tearDownAfterClass() {
        if (PHP_OS === 'Windows') {
            exec('rd /s /q "' . self::$cacheDirectory . '"');
        } else {
            exec('rm -rf "' . self::$cacheDirectory . '"');
        }
    }

    public function testFindClass() {
        $result = $this->inheritanceFinder->findClass('\Synga\InheritanceFinder\Tests\TestClasses\ClassA', $this->projectRoot);

        $this->checkIfArrayContainsPhpClassObject($result);

        if ($result instanceof PhpClass) {
            $this->assertSame(1, $result->getClassType());
        }
    }

    public function testFindExtends() {
        $result = $this->inheritanceFinder->findExtends('\Synga\InheritanceFinder\Tests\TestClasses\ClassA', $this->projectRoot);
        $this->assertCount(2, $result);
        $this->checkIfArrayContainsPhpClassObject($result);
    }

    public function testFindImplements() {
        $result = $this->inheritanceFinder->findImplements('\Synga\InheritanceFinder\Tests\TestClasses\InterfaceA', $this->projectRoot);
        $this->assertCount(3, $result);
        $this->checkIfArrayContainsPhpClassObject($result);
    }

    public function testFindTraitUse() {
        $result = $this->inheritanceFinder->findTraitUse('\Synga\InheritanceFinder\Tests\TestClasses\TraitB', $this->projectRoot);
        $this->assertCount(1, $result);
        $this->checkIfArrayContainsPhpClassObject($result);
    }

    protected function checkIfArrayContainsPhpClassObject($phpClasses) {
        if (is_array($phpClasses)) {
            foreach ($phpClasses as $phpClass) {
                $this->assertInstanceOf('Synga\InheritanceFinder\PhpClass', $phpClass);
            }
        } else {
            $this->assertInstanceOf('Synga\InheritanceFinder\PhpClass', $phpClasses);
        }
    }
}