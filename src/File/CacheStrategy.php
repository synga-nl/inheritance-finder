<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder\File;


use Synga\InheritanceFinder\CacheStrategyInterface;

class CacheStrategy implements CacheStrategyInterface
{
    /**
     * @var FileConfig
     */
    private $config;

    public function __construct(FileConfig $config) {
        $this->config = $config;
    }

    public function get($key) {
        $cachePath = $this->getCachePath($key);
        if (file_exists($cachePath)) {
            $cache = @unserialize(file_get_contents($cachePath));
            if ($cache === false) {
                $cache = [];
            }
        } else {
            $cache = [];
        }

        return $cache;
    }

    public function set($key, $value) {
        return (file_put_contents($this->getCachePath($key), serialize($value)) !== false);
    }

    protected function getCachePath($key) {
        return $this->config->getApplicationRoot() . DIRECTORY_SEPARATOR . $key . '.cache';
    }

    public function getConfig() {
        return $this->config;
    }
}