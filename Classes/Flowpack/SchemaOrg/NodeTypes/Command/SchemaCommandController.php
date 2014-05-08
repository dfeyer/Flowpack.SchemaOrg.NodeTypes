<?php
namespace Flowpack\SchemaOrg\NodeTypes\Command;

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
use Flowpack\SchemaOrg\NodeTypes\Domain\Model\SchemaDefinitions;
use Flowpack\SchemaOrg\NodeTypes\Service\NodeTypeGeneratorService;
use Flowpack\SchemaOrg\NodeTypes\Service\SchemaParserService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;

/**
 * Schema.org Schema Extraction CLI
 */
class SchemaCommandController extends CommandController {

	/**
	 * @Flow\Inject
	 * @var SchemaParserService
	 */
	protected $schemaParserService;

	/**
	 * @Flow\Inject
	 * @var NodeTypeGeneratorService
	 */
	protected $nodeTypeGeneratorService;

	/**
	 * Extract Schema.org to build NodeTypes configuration
	 */
	public function extractCommand() {
		$schemaDefintions = new SchemaDefinitions();
		foreach ($this->schemaParserService->parse($schemaDefintions) as $nodeTypeName=>$nodeType) {
			/** @var NodeType $nodeType */
			$this->outputLine(sprintf("Generate %s ...", $nodeTypeName));
			$this->nodeTypeGeneratorService->generate($nodeType);
		}
	}

}