<?php
namespace Synga\InheritanceFinder\Tests;

use Synga\InheritanceFinder\Cache\CacheRetriever;
use Synga\InheritanceFinder\InheritanceFinderFactory;

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

        $inheritanceFinder = InheritanceFinderFactory::getInheritanceFinder(self::$cacheDirectory);

        $this->cacheRetriever = $inheritanceFinder->getCacheRetriever();

        $this->projectRoot = realpath(__DIR__ . '/TestClasses/');
    }

    public static function tearDownAfterClass() {
        if (PHP_OS === 'Windows') {
            exec('rd /s /q "' . self::$cacheDirectory . '"');
        } else {
            exec('rm -rf "' . self::$cacheDirectory . '"');
        }
    }

    public function testRetrieveCacheWarmUp() {
        $this->cacheRetriever->retrieve($this->projectRoot);
    }

    public function testRetrieveCacheFromCache() {
        $this->cacheRetriever->retrieve($this->projectRoot);
    }
}