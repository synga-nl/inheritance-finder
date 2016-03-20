<?php

/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */
class FastArrayAccessHelperTest extends TestCase
{
    public function testFastArrayAccessHelper() {
        $fastArrayAccessHelper = new \Synga\InheritanceFinder\Helpers\FastArrayAccessHelper();

        $phpClass = new \Synga\InheritanceFinder\PhpClass();
        $phpClass->setFile(new \Symfony\Component\Finder\SplFileInfo(__DIR__, __DIR__, __DIR__));

        $arrayTest = [$phpClass];

        $fastArrayAccessHelperResult = $fastArrayAccessHelper->getPathnameArray($arrayTest);

        $this->assertCount(1, $fastArrayAccessHelperResult);
        $this->assertSame(__DIR__, key($fastArrayAccessHelperResult));
    }
}