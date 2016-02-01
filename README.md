# Inheritance Finder
This package can find classes which are extended, implemented or is using a certain trait.

##Usage

```
$config = new \Synga\InheritanceFinder\File\FileConfig();
$config->setApplicationRoot(__DIR__);
$config->setCacheDirectory(__DIR__);

$inheritanceFinder = Synga\InheritanceFinder\InheritanceFinderFactory::getInheritanceFinder($config);
$inheritanceFinder->findExtends('SomeNamespace\OtherNamespacePath\Class');
```

It will now find all classes which inherit from class `SomeNamespace\OtherNamespacePath\Class` whithin your current directory

##Possible options:

* `findClass('full qualified namespace')` - Can locate a file with the given namespace in the given directory
* `findExtends('full qualified namespace')` - Can locate multiple classes who inherit from the given class in the given directory
* `findImplements('full qualified namespace')` - Can locate multiple classes who inherit from the given interface in the given directory
* `findTraitUse('full qualified namespace')` - Can locate multiple classes who uses the given trait in the given directory
* `findMultiple('Classes [string or array of strings]', 'Interfaces [string or array of strings]', 'Traits [string or array of strings]')` - Can locate multiple classes at once.


##Acknowledgements:
* Right now this package cannot handle multiple classes in one file. I will try to fix this in the near future
* Right now you can't combine two search methods like searching for an interface and a trait. I am thinking of implement this.
