# Inheritance Finder
This package can find classes which are extended, implemented or is using a certain trait.

##Usage

```
$inheritanceFinderFactory = new inheritanceFinderFactory();
$inheritanceFinder = $inheritanceFinderFactory->getInheritanceFinder('path/to/project/root');
$inheritanceFinder->findExtends('SomeNamespace\OtherNamespacePath\Class');
```

It will now find all classes which inherit from class `SomeNamespace\OtherNamespacePath\Class` whithin your `path/to/project/root`

##Possible options:

* `findClass('full qualified namespace', 'directory to search in')` - Can locate a file with the given namespace in the given directory
* `findExtends('full qualified namespace', 'directory to search in')` - Can locate multiple classes who inherit from the given class in the given directory
* `findImplements('full qualified namespace', 'directory to search in')` - Can locate multiple classes who inherit from the given interface in the given directory
* `findTraitUse('full qualified namespace', 'directory to search in')` - Can locate multiple classes who uses the given trait in the given directory


##Acknowledgements:
* Right now this package cannot handle multiple classes in one file. I will try to fix this in the near future
* Right now you can't combine two search methods like searching for an interface and a trait. I am thinking of implement this.
