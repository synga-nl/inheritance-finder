<?php
namespace Synga\InheritanceFinder\Cache\Strategy;

/**
 * Stores information in a file.
 *
 * Class FileCacheStrategy
 * @package Synga\InheritanceFinder\Cache\Strategy
 */
class FileCacheStrategy implements CacheStrategyInterface
{
    /**
     * The directory which should use to store the cache
     *
     * @var string
     */
    private $cacheDirectory;

    /**
     * FileCacheStrategy constructor.
     * @param $cacheDirectory
     */
    public function __construct($cacheDirectory) {
        $cacheDirectory = realpath($cacheDirectory);
        if (!file_exists($cacheDirectory)) {
            throw new \InvalidArgumentException('The given cache directory "' . $cacheDirectory . '" does not exist');
        }

        $this->cacheDirectory = $cacheDirectory . '/';
    }

    /**
     * {@inheritdoc}
     *
     * @param $key
     * @param $data
     * @param int $expireTime
     * @return bool
     */
    public function set($key, $data, $expireTime = -1) {
        $filePath = $this->getCachePath($key);

        return (file_put_contents($filePath, serialize(['expire' => ($expireTime === -1) ? -1 : time() + $expireTime, 'data' => $data])) !== false);
    }

    /**
     * {@inheritdoc}
     *
     * @param $key
     * @return mixed
     */
    public function get($key) {
        $cache = $this->exists($key, true);

        if ($cache === false) {
            return false;
        }

        return $cache['data'];
    }

    /**
     * {@inheritdoc}
     *
     * @param $directory
     * @param bool $returnData
     * @return bool|mixed
     */
    public function exists($key, $returnData = false) {
        $key = $this->getCachePath($key);

        if (file_exists($key)) {
            $data = unserialize(file_get_contents($key));

            if (isset($data['expire']) && ($data['expire'] >= time() || $data['expire'] === -1)) {
                if ($returnData === true) {
                    return $data;
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the full cache path so we can check if the file exists
     *
     * @param $key
     * @return string
     */
    protected function getCachePath($key) {
        return $this->cacheDirectory . md5($key) . '.cache';
    }

    /**
     * Getter for the base cache directory
     *
     * @return string
     */
    public function getCacheDirectory() {
        return $this->cacheDirectory;
    }
}