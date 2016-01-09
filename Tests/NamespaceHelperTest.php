<?php
namespace Synga\InheritanceFinder\Tests;

include_once 'TraitHelpers/NamespaceHelperTraitPublic.php';

use Synga\InheritanceFinder\Tests\TraitHelpers\NamespaceHelperTraitHelper;

class NamespaceHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $namespaceHelper;

    protected $testNamespace = 'My\\Test\\Namespace';

    protected function setUp() {
        $this->namespaceHelper = new NamespaceHelperTraitHelper();
    }

    public function testEscapeNamespace() {
        $pregMatch = preg_match('/' . $this->namespaceHelper->escapeNamespace($this->testNamespace) . '/', $this->testNamespace);

        $this->assertSame(1, $pregMatch);
    }

    public function testGetFullQualifiedNamespace(){
        $this->assertSame($this->testNamespace, $this->namespaceHelper->getFullQualifiedNamespace('My\\Test', 'Namespace'));
    }

    public function testExtractNamespace(){
        $extracted = $this->namespaceHelper->extractNamespace($this->testNamespace);

        $this->assertSame([
            'namespace' => 'My\\Test',
            'class' => 'Namespace',
            'full_qualified_namespace' => 'My\\Test\\Namespace',
        ], $extracted);
    }
}
