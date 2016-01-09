<?php
namespace Synga\InheritanceFinder\Tests;

use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Synga\InheritanceFinder\PhpClass;
use Synga\InheritanceFinder\PhpFileFinder;

class PhpFileFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Synga\InheritanceFinder\PhpFileFinder
     */
    protected $phpFileFinder;

    protected static $cacheFile = __DIR__ . '/classes.php';

    protected $projectRoot;

    protected function setUp() {
        $finder              = new Finder();
        $this->phpFileFinder = new PhpFileFinder(self::$cacheFile, $finder, (new ParserFactory)->create(ParserFactory::PREFER_PHP7));
        $this->projectRoot = realpath(__DIR__ . '/TestClasses/');
    }

    public static function tearDownAfterClass(){
        unlink(self::$cacheFile);
    }

    public function testBuildCache(){
        $this->phpFileFinder->buildCache($this->projectRoot);
        $this->assertFileExists(self::$cacheFile);
        $this->assertTrue(strlen(file_get_contents(self::$cacheFile)) > 0);
    }

    public function testFindClass() {
        $result = $this->phpFileFinder->findClass('\Synga\InheritanceFinder\Tests\TestClasses\ClassA');

        $this->checkIfArrayContainsPhpClassObject($result);

        if ($result instanceof PhpClass) {
            $this->assertSame(1, $result->getClassType());
        }
    }

    public function testFindExtends() {
        $result = $this->phpFileFinder->findExtends('\Synga\InheritanceFinder\Tests\TestClasses\ClassA');
        $this->assertCount(2, $result);
        $this->checkIfArrayContainsPhpClassObject($result);
    }

    public function testFindImplements(){
        $result = $this->phpFileFinder->findImplements('\Synga\InheritanceFinder\Tests\TestClasses\InterfaceA');
        $this->assertCount(3, $result);
        $this->checkIfArrayContainsPhpClassObject($result);
    }

    public function testFindTraitUse(){
        $result = $this->phpFileFinder->findTraitUse('\Synga\InheritanceFinder\Tests\TestClasses\TraitB');
        $this->assertCount(1, $result);
        $this->checkIfArrayContainsPhpClassObject($result);
    }

    protected function checkIfArrayContainsPhpClassObject($phpClasses){
        if(is_array($phpClasses)){
            foreach($phpClasses as $phpClass){
                $this->assertInstanceOf('Synga\InheritanceFinder\PhpClass', $phpClass);
            }
        } else {
            $this->assertInstanceOf('Synga\InheritanceFinder\PhpClass', $phpClasses);
        }
    }
}