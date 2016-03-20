<?php

/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */
class NamespaceHelperStub
{
    use \Synga\InheritanceFinder\NamespaceHelper;

    public function __call($name, $arguments) {
        return call_user_func_array([$this, $name], $arguments);
    }
}