<?php
/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */

namespace Synga\InheritanceFinder;


use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Synga\InheritanceFinder\Parser\PhpClassParser;
use Synga\InheritanceFinder\Helpers\FastArrayAccessHelper;

/**
 * Class CacheBuilder
 * @package Synga\InheritanceFinder
 */
class CacheBuilder implements CacheBuilderInterface
{
    /**
     * @var CacheStrategyInterface
     */
    private $cacheStrategy;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var PhpClassParser
     */
    private $phpClassParser;

    /**
     * @var FastArrayAccessHelper
     */
    private $arrayHelper;

    /**
     * CacheBuilder constructor.
     * @param CacheStrategyInterface $cacheStrategy
     * @param PhpClassParser $phpClassParser
     * @param Finder $finder
     * @param FastArrayAccessHelper $arrayHelper
     */
    public function __construct(CacheStrategyInterface $cacheStrategy, PhpClassParser $phpClassParser, Finder $finder, FastArrayAccessHelper $arrayHelper) {
        $this->cacheStrategy  = $cacheStrategy;
        $this->finder         = $finder;
        $this->phpClassParser = $phpClassParser;
        $this->arrayHelper    = $arrayHelper;
    }

    /**
     * @return mixed
     */
    public function getCache() {
        $cacheKey = md5('inheritance_finder');

        $retrievedCache = $this->cacheStrategy->get($cacheKey);
        $cache          = $this->build($cacheKey, $retrievedCache, new PhpClass());

        return $cache['data'];
    }

    /**
     * @param $cacheKey
     * @param $cache
     * @param \Synga\InheritanceFinder\PhpClass $phpClassClone
     * @return mixed
     */
    protected function build($cacheKey, $cache, PhpClass $phpClassClone) {
        if (empty($cache)) {
            foreach ($this->findFiles() as $file) {
                $cache['data'][] = $this->parseSplFileInfo($file, $phpClassClone);
            }
        } else {
            $pathnameArray  = $this->arrayHelper->getPathnameArray($cache['data']);
            $this->removeNonExistentClasses($pathnameArray);
            $this->addNewClasses($pathnameArray, (($cache['composer_lock_md5'] == $this->getComposerLockMd5()) ? true : false), $phpClassClone);
            $this->modifyModifiedClasses($pathnameArray);

            $cache['data'] = array_values($pathnameArray);

            $this->setCache($cacheKey, $cache['data']);
        }

        $cache['data'] = array_filter($cache['data']);

        $this->setCache($cacheKey, $cache);

        return $cache;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @param \Synga\InheritanceFinder\PhpClass $phpClassClone
     * @return \Synga\InheritanceFinder\PhpClass
     */
    protected function parseSplFileInfo(SplFileInfo $fileInfo, PhpClass $phpClassClone) {
        $phpClass = clone $phpClassClone;
        $result   = $this->phpClassParser->parse($phpClass, $fileInfo);

        if ($result !== false) {
            return $phpClass;
        }
    }


    /**
     * Removes files which are no longer present in this directory
     *
     * @param PhpClass[] $pathnameArray
     * @return \Synga\InheritanceFinder\PhpClass[]
     */
    protected function removeNonExistentClasses(&$pathnameArray) {
        foreach ($pathnameArray as $pathname => $phpClass) {
            if (!file_exists($pathname)) {
                unset($pathnameArray[$pathname]);
            }
        }
    }

    /**
     * Checks if a file is modified and if so, reparse the file
     *
     * @param phpClass[] $pathnameArray
     * @return \Synga\InheritanceFinder\PhpClass[]
     *
     * @todo make use of the pathnameArray so duplicate namespaces are not lost.
     */
    protected function modifyModifiedClasses(&$pathnameArray) {
        foreach ($pathnameArray as $pathname => $phpClass) {
            if (file_exists($phpClass->getFile()->getPathname()) && $phpClass->getLastModified()->getTimestamp() != $phpClass->getFile()->getMTime()) {
                $file = $phpClass->getFile();

                $phpClass->clear();

                $result = $this->phpClassParser->parse($phpClass, $file);
                if ($result === false) {
                    unset($pathnameArray[$pathname]);
                }
            }
        }
    }

    /**
     * Finds classes which are not added to the cache
     *
     * @param $pathnameArray
     * @param bool $excludeVendor
     * @param PhpClass $phpClassClone
     */
    protected function addNewClasses(&$pathnameArray, $excludeVendor = true, PhpClass $phpClassClone) {
        $files = $this->findFiles($excludeVendor);

        foreach ($files as $file) {
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            $pathname = $file->getPathname();
            if (!isset($pathnameArray[$pathname])) {
                $phpClass = clone $phpClassClone;
                $result   = $this->phpClassParser->parse($phpClass, $file);

                if ($result !== false) {
                    $pathnameArray[$pathname]                               = $phpClass;
                }
            }
        }
    }

    /**
     * @param bool $excludeVendor
     * @return Finder
     */
    protected function findFiles($excludeVendor = false) {
        $finder = $this->finder->create();
        $finder->files()->name('*.php')->contains('class')->contains('trait')->contains('interface');
        if ($excludeVendor) {
            $finder->notPath('/^vendor/');
        }

        return $finder->in($this->cacheStrategy->getConfig()->getApplicationRoot());
    }

    /**
     * @param $cacheKey
     * @param $cache
     */
    protected function setCache($cacheKey, $cache) {
        $this->cacheStrategy->set($cacheKey, [
            'timestamp'         => time(),
            'composer_lock_md5' => $this->getComposerLockMd5(),
            'data'              => $cache['data']
        ]);
    }

    /**
     * @return string
     */
    protected function getComposerLockMd5() {
        return md5_file($this->cacheStrategy->getConfig()->getApplicationRoot() . '/composer.lock');
    }
}