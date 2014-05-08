<?php
namespace TYPO3\Neos\Tests\Unit\Domain\Model;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SchemaOrg.NodeTypes". *
 *                                                                               *
 * It is free software; you can redistribute it and/or modify it under           *
 * the terms of the GNU Lesser General Public License, either version 3          *
 * of the License, or (at your option) any later version.                        *
 *                                                                               *
 * The TYPO3 project - inspiring people to share!                                *
 *                                                                               */

use Flowpack\SchemaOrg\NodeTypes\Domain\Model\NodeType;
use TYPO3\Flow\Tests\UnitTestCase;

/**
 * NodeType Tests
 */
class NodeTypeTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function getNameReturnTheName() {
		$nodeType = new NodeType('Foo');
		$this->assertSame('Foo', $nodeType->getName());
	}

	/**
	 * @test
	 */
	public function getAbstractReturnTheAbstractValueByDefaultFalse() {
		$nodeType = new NodeType('Foo');
		$this->assertSame(FALSE, $nodeType->getAbstract());
		$nodeType = new NodeType('Foo', TRUE);
		$this->assertSame(TRUE, $nodeType->getAbstract());
	}

	/**
	 * @test
	 */
	public function getFinalReturnTheFinalValueByDefaultFalse() {
		$nodeType = new NodeType('Foo');
		$this->assertSame(FALSE, $nodeType->getFinal());
		$nodeType = new NodeType('Foo', TRUE, TRUE);
		$this->assertSame(TRUE, $nodeType->getFinal());
	}

	/**
	 * @test
	 */
	public function getConfigurationReturnAnArray() {
		$nodeType = new NodeType('Foo');
		$this->assertSame(array(), $nodeType->getConfiguration());
	}

	/**
	 * @test
	 */
	public function setConfigurationByPathUpdateTheCurrentConfiguration() {
		$nodeType = new NodeType('Foo');
		$nodeType->setConfigurationByPath('foo.fii', 'Hello World');
		$this->assertSame(array(
			'foo' => array(
				'fii' => 'Hello World'
			)
		), $nodeType->getConfiguration());
	}

	/**
	 * @test
	 */
	public function getSuperTypeReturnAnArray() {
		$nodeType = new NodeType('Foo');
		$this->assertSame(array(), $nodeType->getAncestors());
	}

	/**
	 * @test
	 */
	public function addSuperTypeRegisterNewSuperType() {
		$nodeType = new NodeType('Foo');
		$nodeType->addAncestor('Flowpack.SchemaOrg.NodeTypes:Test');
		$this->assertSame(array(
			'Flowpack.SchemaOrg.NodeTypes:Test' => TRUE
		), $nodeType->getAncestors());
	}
}