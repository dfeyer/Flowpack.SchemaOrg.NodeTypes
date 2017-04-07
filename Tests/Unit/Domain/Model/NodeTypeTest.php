<?php
namespace Neos\Neos\Tests\Unit\Domain\Model;

/*                                                                               *
 * This script belongs to the Neos Flow package "Flowpack.SchemaOrg.NodeTypes".  *
 *                                                                               */

use Flowpack\SchemaOrg\NodeTypes\Domain\Model\NodeType;
use Neos\Flow\Tests\UnitTestCase;

/**
 * NodeType Tests
 */
class NodeTypeTest extends UnitTestCase
{

    /**
     * @test
     */
    public function getNameReturnTheName()
    {
        $nodeType = new NodeType('Foo');
        $this->assertSame('Foo', $nodeType->getName());
    }

    /**
     * @test
     */
    public function getAbstractReturnTheAbstractValueByDefaultFalse()
    {
        $nodeType = new NodeType('Foo');
        $this->assertSame(false, $nodeType->getAbstract());
        $nodeType = new NodeType('Foo', true);
        $this->assertSame(true, $nodeType->getAbstract());
    }

    /**
     * @test
     */
    public function getFinalReturnTheFinalValueByDefaultFalse()
    {
        $nodeType = new NodeType('Foo');
        $this->assertSame(false, $nodeType->getFinal());
        $nodeType = new NodeType('Foo', true, true);
        $this->assertSame(true, $nodeType->getFinal());
    }

    /**
     * @test
     */
    public function getConfigurationReturnAnArray()
    {
        $nodeType = new NodeType('Foo');
        $this->assertSame([], $nodeType->getConfiguration());
    }

    /**
     * @test
     */
    public function setConfigurationByPathUpdateTheCurrentConfiguration()
    {
        $nodeType = new NodeType('Foo');
        $nodeType->setConfigurationByPath('foo.fii', 'Hello World');
        $this->assertSame([
            'foo' => [
                'fii' => 'Hello World',
            ],
        ], $nodeType->getConfiguration());
    }

    /**
     * @test
     */
    public function getSuperTypeReturnAnArray()
    {
        $nodeType = new NodeType('Foo');
        $this->assertSame([], $nodeType->getSuperTypes());
    }

    /**
     * @test
     */
    public function addSuperTypeRegisterNewSuperType()
    {
        $nodeType = new NodeType('Foo');
        $nodeType->addSuperType('Flowpack.SchemaOrg.NodeTypes:Test');
        $this->assertSame([
            'Flowpack.SchemaOrg.NodeTypes:Test' => true,
        ], $nodeType->getSuperTypes());
    }
}
