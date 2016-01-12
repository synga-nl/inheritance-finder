<?php
namespace Synga\InheritanceFinder\Tests;

use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Synga\InheritanceFinder\Cache\CacheBuilder;
use Synga\InheritanceFinder\Cache\CacheRetriever;
use Synga\InheritanceFinder\Cache\Strategy\FileCacheStrategy;
use Synga\InheritanceFinder\Parser\Visitors\ClassNodeVisitor;
use Synga\InheritanceFinder\PhpClass;

class CacheRetrieverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheRetriever
     */
    protected $cacheRetriever;

    protected static $cacheDirectory = __DIR__ . '/cache/';

    protected $projectRoot;

    protected function setUp() {
        if (!file_exists(self::$cacheDirectory)) {
            mkdir(self::$cacheDirectory);
        }
        $this->cacheRetriever = new CacheRetriever(
            new FileCacheStrategy(self::$cacheDirectory),
            new CacheBuilder(
                new ClassNodeVisitor(),
                new Finder(),
                (new ParserFactory)->create(ParserFactory::PREFER_PHP7)),
            new PhpClass()
        );

        $this->projectRoot = realpath(__DIR__ . '/TestClasses/');
    }

//    public static function tearDownAfterClass() {
//        if (PHP_OS === 'Windows') {
//            exec('rd /s /q "' . self::$cacheDirectory . '"');
//        } else {
//            exec('rm -rf "' . self::$cacheDirectory . '"');
//        }
//    }

    public function testRetrieveCacheWarmUp() {
        $this->cacheRetriever->retrieve($this->projectRoot);
    }

    public function testRetrieveCacheFromCache() {
        $this->cacheRetriever->retrieve($this->projectRoot);
    }
}