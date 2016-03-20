<?php

/**
 * Synga Inheritance Finder
 * @author      Roy Pouls
 * @copytright  2016 Roy Pouls / Synga (http://www.synga.nl)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/synga-nl/inheritance-finder
 */
class NamespaceHelperTraitTest extends TestCase
{
    /**
     * @var NamespaceHelperStub
     */
    protected $namespaceHelper;

    protected function setUp(){
        include_once __DIR__ . '/stubs/NamespaceHelperStub.php';
        $this->namespaceHelper = new NamespaceHelperStub();
    }

    public function testGetFullQualifiedNamespace(){
        $fullQualifiedNamespace = 'Full\Qualified\Namespace';
        $class     = 'Namespace';
        $namespace = 'Full\Qualified';

        $this->assertSame($fullQualifiedNamespace, $this->namespaceHelper->getFullQualifiedNamespace($namespace, $class));
    }

    public function testExtractNamespace(){
        $fullQualifiedNamespace = 'Full\Qualified\Namespace';
        $class     = 'Namespace';
        $namespace = 'Full\Qualified';

        $result = $this->namespaceHelper->extractNamespace($fullQualifiedNamespace);

        $this->assertSame($fullQualifiedNamespace, $result['full_qualified_namespace']);
        $this->assertSame($class, $result['class']);
        $this->assertSame($namespace, $result['namespace']);

    }
}