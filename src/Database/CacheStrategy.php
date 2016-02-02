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

/**
 * Class CacheStrategy
 * @package Synga\InheritanceFinder\Database
 */
class CacheStrategy implements CacheStrategyInterface
{
    /**
     * @var DatabaseConfig
     */
    private $config;

    /**
     * CacheStrategy constructor.
     * @param DatabaseConfig $config
     */
    public function __construct(DatabaseConfig $config) {
        $this->config = $config;
    }

    /**
     * @param $key
     * @return array|void
     */
    public function get($key) {
        // TODO: Implement get() method.
    }

    /**
     * @param $key
     * @param $value
     * @return mixed|void
     */
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