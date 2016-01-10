<?php
namespace Synga\InheritanceFinder\Cache\Strategy;

/**
 * An interface which can set or get data and can check if the data is still valid.
 *
 * Interface CacheStrategyInterface
 * @package Synga\InheritanceFinder\Cache\Strategy
 */
interface CacheStrategyInterface
{
    /**
     * Set data based on a certain key with an expire time
     *
     * @param $key
     * @param $data
     * @param int $expireTime
     * @return mixed
     */
    public function set($key, $data, $expireTime = -1);

    /**
     * Get data based on a certain key
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * Check if cache exists and is not expired
     *
     * @param $key
     * @param $returnData
     * @return mixed
     */
    public function exists($key, $returnData);
}