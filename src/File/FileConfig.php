<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder\File;


use Synga\InheritanceFinder\ConfigInterface;

class FileConfig implements ConfigInterface
{
    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var string
     */
    private $applicationRoot;

    /**
     * @var int
     */
    private $expireTime = 1209600;

    /**
     * @var bool
     */
    private $wipeCache = false;

    /**
     * @return string
     */
    public function getCacheDirectory() {
        return $this->cacheDirectory;
    }

    /**
     * @param string $cacheDirectory
     */
    public function setCacheDirectory($cacheDirectory) {
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @return string
     */
    public function getApplicationRoot() {
        return $this->applicationRoot;
    }

    /**
     * @param string $applicationRoot
     */
    public function setApplicationRoot($applicationRoot) {
        $this->applicationRoot = $applicationRoot;
    }

    /**
     * @return int
     */
    public function getExpireTime() {
        return $this->expireTime;
    }

    /**
     * @param int $expireTime
     */
    public function setExpireTime($expireTime) {
        $this->expireTime = $expireTime;
    }

    /**
     * @return boolean
     */
    public function isWipeCache() {
        return $this->wipeCache;
    }

    /**
     * @param boolean $wipeCache
     */
    public function setWipeCache($wipeCache) {
        $this->wipeCache = $wipeCache;
    }
}