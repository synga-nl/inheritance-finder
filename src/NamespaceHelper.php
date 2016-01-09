<?php
namespace Synga\InheritanceFinder;

/**
 * A trait that can handle namespaces
 *
 * Class NamespaceHelper
 * @package Synga\InheritanceFinder
 */
trait NamespaceHelper
{
    /**
     * Escapes the namespace full qualified namespace so we can use it in a regex
     *
     * @param $fullQualifiedNamespace
     * @return string
     */
    protected function escapeNamespace($fullQualifiedNamespace) {
        return implode('\\\\', explode('\\', $fullQualifiedNamespace));
    }

    /**
     * Concats the namespace and the class to the full qualified namespace
     *
     * @param $namespace
     * @param $class
     * @return string
     */
    protected function getFullQualifiedNamespace($namespace, $class) {
        return $namespace . '\\' . $class;
    }

    /**
     * Parses the full qualified namespace to the namespace, class and the full qualified namespace
     *
     * @param string $fullQualifiedNamespace
     * @return array
     */
    protected function extractNamespace($fullQualifiedNamespace) {
        $parts     = explode('\\', $fullQualifiedNamespace);
        $copyParts = $parts;
        $copyParts = array_splice($copyParts, 0, -1);

        return [
            'namespace'                => implode('\\', $copyParts),
            'class'                    => end($parts),
            'full_qualified_namespace' => $fullQualifiedNamespace
        ];
    }
}