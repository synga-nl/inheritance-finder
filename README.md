# Inheritance Finder
This package can find classes which are extended, implemented or is using a certain trait. It builds a cache and uses it to find the desired files.

##Usage

```
$config = new \Synga\InheritanceFinder\File\FileConfig();
$config->setApplicationRoot(__DIR__);
$config->setCacheDirectory(__DIR__);

$inheritanceFinder = Synga\InheritanceFinder\InheritanceFinderFactory::getInheritanceFinder($config);
$inheritanceFinder->findExtends('SomeNamespace\OtherNamespacePath\Class');
```

It will now find all classes which inherit from class `SomeNamespace\OtherNamespacePath\Class` whithin your `ApplicationRoot` directory

##Possible options:

* `findClass('full qualified namespace')` - Can locate a file with the given namespace in the given directory
* `findExtends('full qualified namespace')` - Can locate multiple classes which inherit from the given class in the given directory
* `findImplements('full qualified namespace')` - Can locate multiple classes which implement or inherit from the given interface in the given directory
* `findTraitUse('full qualified namespace')` - Can locate multiple classes which uses the given trait in the given directory
* `findMultiple('Classes [string or array of strings]', 'Interfaces [string or array of strings]', 'Traits [string or array of strings]')` - Can locate multiple classes at once, it uses the methods discribed above.


##Acknowledgements:
* Right now this package cannot handle multiple classes in one file. I will try to fix this in the near future.
* The first run will be slow, because it is indexing all the files in your project root. I'm thinking off a manner to make it faster. I am planning of using react php for this so we can use multiple processes.
* Don't use this code in production (you can use it FOR production, to build a cache of files). The performance results are strongly depending your hardware.