<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder\Database;


use Synga\InheritanceFinder\CacheStrategyInterface;

class CacheStrategy implements CacheStrategyInterface
{
    /**
     * @var DatabaseConfig
     */
    private $config;

    public function __construct(DatabaseConfig $config) {
        $this->config = $config;
    }

    public function get($key) {
        // TODO: Implement get() method.
    }

    public function set($key, $value) {
        // TODO: Implement set() method.
    }

    /**
     * @return DatabaseConfig
     */
    public function getConfig() {
        return $this->config;
    }
}