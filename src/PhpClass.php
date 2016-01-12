<?php
namespace Synga\InheritanceFinder;

use Symfony\Component\Finder\SplFileInfo;

/**
 * This class contains the data of a file that contains al class, abstract class, interface or trait
 *
 * @todo make checks for extends, implements and trait use so you don't have to do these over and over again when you use it
 *
 * Class PhpClass
 * @package Synga\InheritanceFinder
 */
class PhpClass
{
    use NamespaceHelper {
        NamespaceHelper::getFullQualifiedNamespace as getFullQualifiedNamespaceTrait;
    }

    /**
     * Types of classes
     */
    const TYPE_CLASS = 1;
    const TYPE_ABSTRACT_CLASS = 2;
    const TYPE_FINAL_CLASS = 3;
    const TYPE_INTERFACE = 4;
    const TYPE_TRAIT = 5;

    /**
     * The class name
     *
     * @var string
     */
    protected $class;

    /**
     * The namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * The full qualified namespace
     *
     * @var string
     */
    protected $fullQualifiedNamespace;

    /**
     * The file to the class
     *
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    protected $file;

    /**
     * @var \DateTime
     */
    protected $lastModified;

    /**
     * The class type of the class. Check the TYPE_ constants for possible values
     *
     * @var int
     */
    protected $classType;

    /**
     * The full qualified namespace which this class extends
     *
     * @var string
     */
    protected $extends;

    /**
     * An array of full qualified namespaces which interfaces this class implements
     *
     * @var string[]
     */
    protected $implements = [];

    /**
     * An array of full qualified namespaces which traits this class uses
     *
     * @var string[]
     */
    protected $traits = [];

    /**
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class) {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    /**
     * When the full qualified namespace is empty but the class and namespace are set, we concat these two values to the
     * full qualified namespace
     *
     * @return string
     */
    public function getFullQualifiedNamespace() {
        if(empty($this->fullQualifiedNamespace) && !empty($this->class)){
            $this->fullQualifiedNamespace = $this->getFullQualifiedNamespaceTrait($this->namespace, $this->class);
        }

        return $this->fullQualifiedNamespace;
    }

    /**
     * When we set the full qualified namespace, we automatically set the class and the namespace
     *
     * @param string $fullQualifiedNamespace
     */
    public function setFullQualifiedNamespace($fullQualifiedNamespace) {
        if (empty($this->class) && empty($this->namespace)) {
            $extracted       = $this->extractNamespace($fullQualifiedNamespace);
            $this->class     = $extracted['class'];
            $this->namespace = $extracted['namespace'];
        }


        $this->fullQualifiedNamespace = $fullQualifiedNamespace;
    }

    /**
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $file
     */
    public function setFile($file) {
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getClassType() {
        return $this->classType;
    }

    /**
     * @param int $classType
     */
    public function setClassType($classType) {
        $this->classType = $classType;
    }

    /**
     * @return string
     */
    public function getExtends() {
        return $this->extends;
    }

    /**
     * @param string $extends
     */
    public function setExtends($extends) {
        $this->extends = $extends;
    }

    /**
     * @return \string[]
     */
    public function getImplements() {
        return $this->implements;
    }

    /**
     * @param \string $implements
     */
    public function setImplements($implements) {
        $this->implements[] = $implements;
    }

    /**
     * @return \string[]
     */
    public function getTraits() {
        return $this->traits;
    }

    /**
     * @param \string $traits
     */
    public function setTraits($traits) {
        $this->traits[] = $traits;
    }

    /**
     * @return \DateTime
     */
    public function getLastModified() {
        return $this->lastModified;
    }

    /**
     * @param \DateTime $lastModified
     */
    public function setLastModified(\DateTime $lastModified) {
        $this->lastModified = $lastModified;
    }

    /**
     * Because SplFileInfo can't be serialized, we need to replace it with data that can be recovered when we serialize
     * this object again.
     *
     * @return array
     */
    public function __sleep() {
        $fileInfo = [
            'file' => $this->file->getPathname(),
            'relativePath' => $this->file->getRelativePath(),
            'relativePathname' => $this->file->getRelativePathname()
        ];

        $classVars = get_object_vars($this);
        $this->file = $fileInfo;

        return array_keys($classVars);
    }

    /**
     * Re-instantiate the SplFileInfo during serialization
     */
    public function __wakeup() {
        $this->file = new SplFileInfo($this->file['file'], $this->file['relativePath'], $this->file['relativePathname']);
    }
}