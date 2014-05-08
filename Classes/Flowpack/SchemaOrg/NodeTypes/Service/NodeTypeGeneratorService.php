<?php
namespace Flowpack\SchemaOrg\NodeTypes\Service;

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
use Flowpack\SchemaOrg\NodeTypes\Domain\Model\Property;
use Flowpack\SchemaOrg\NodeTypes\Domain\Model\SchemaDefinitions;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Utility\Arrays;

/**
 * Node Type Generator
 *
 * @Flow\Scope("singleton")
 */
class NodeTypeGeneratorService {

	/**
	 * @Flow\Inject(setting="schemas.storagePath")
	 * @var string
	 */
	protected $storagePath;

	/**
	 * @param NodeType $nodeType
	 * @return void
	 */
	public function generate(NodeType $nodeType) {
		#\TYPO3\Flow\var_dump($nodeType);
	}

}