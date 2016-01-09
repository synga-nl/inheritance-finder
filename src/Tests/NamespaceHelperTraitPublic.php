<?php
namespace Synga\InheritanceFinder\Tests;

use Synga\InheritanceFinder\NamespaceHelper;

class NamespaceHelperTraitPublic
{
    use NamespaceHelper;

    public function __call($name, $arguments) {
        if(method_exists($this, $name)){
            return call_user_func_array([$this, $name], $arguments);
        }

        return null;
    }
}