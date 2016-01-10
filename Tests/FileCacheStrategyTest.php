<?php
namespace Synga\InheritanceFinder\Tests;

use Synga\InheritanceFinder\Cache\Strategy\FileCacheStrategy;

class FileCacheStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileCacheStrategy;
     */
    protected $fileCacheStrategy;

    /**
     * @var string
     */
    protected static $cacheDirectory = __DIR__ . '/cache/';

    protected $cacheKey;

    protected $cacheData = ['this', 'is' => 'test', 'data'];

    protected function setUp() {
        if (!file_exists(self::$cacheDirectory)) {
            mkdir(self::$cacheDirectory);
        }

        $this->fileCacheStrategy = new FileCacheStrategy(self::$cacheDirectory);
        $this->cacheKey = realpath(__DIR__ . '/../src');
    }

    public static function tearDownAfterClass() {
        if (PHP_OS === 'Windows') {
            exec('rd /s /q "' . self::$cacheDirectory . '"');
        } else {
            exec('rm -rf "' . self::$cacheDirectory . '"');
        }
    }

    public function testCreateInstanceWithTrailingSlash() {
        $instance = new FileCacheStrategy(__DIR__ . '/');
        $this->assertSame(__DIR__ . '/', $instance->getCacheDirectory());
    }

    public function testCreateInstanceWithoutTrailingSlash() {
        $instance = new FileCacheStrategy(__DIR__);
        $this->assertSame(__DIR__ . '/', $instance->getCacheDirectory());
    }

    public function testSetCache(){
        $this->fileCacheStrategy->set($this->cacheKey, $this->cacheData, 400);
        $this->assertFileExists(self::$cacheDirectory . md5($this->cacheKey) . '.cache');
    }

    public function testGetCache(){
        $result = $this->fileCacheStrategy->get($this->cacheKey);
        $this->assertSame($this->cacheData, $result);
    }

    public function testGetCacheExistBool(){
        $this->assertTrue($this->fileCacheStrategy->exists($this->cacheKey, false));
    }

    public function testGetCacheExistData(){
        $result = $this->fileCacheStrategy->exists($this->cacheKey, true);
        $this->assertSame($this->cacheData, $result['data']);
    }
}
