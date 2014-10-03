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
use Flowpack\SchemaOrg\NodeTypes\Service\ConfigurationService;
use Flowpack\SchemaOrg\NodeTypes\Service\NodeTypeBuilder;
use Flowpack\SchemaOrg\NodeTypes\Service\SchemaParserService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\TYPO3CR\Domain\Service\NodeTypeManager;
use TYPO3\TYPO3CR\Exception\NodeTypeNotFoundException;

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
	 * @var NodeTypeManager
	 */
	protected $nodeTypeManager;

	/**
	 * @Flow\Inject
	 * @var NodeTypeBuilder
	 */
	protected $nodeTypeBuilder;

	/**
	 * @Flow\Inject
	 * @var ConfigurationService
	 */
	protected $configurationService;

	/**
	 * @Flow\Inject(setting="schemas.jsonFilename")
	 * @var string
	 */
	protected $jsonSchema;

	/**
	 * Extract Schema.org to build NodeTypes configuration
	 *
	 * @param string $name The name of the file
	 * @param string $packageKey The package key
	 * @param string $type The Type from schema.org
	 */
	public function extractCommand($name = NULL, $packageKey = NULL, $type = NULL) {
		$name = $name ?: 'Default';
		$this->configurationService->setPackageKey($packageKey);
		$this->outputLine();
		$this->outputFormatted("# Extracting schema.org ...");

		$this->schemaParserService->setAllSchemaJsonFilename($this->jsonSchema);

		if ($type !== NULL) {
			$nodeTypes = $this->schemaParserService->parseByTypes(explode(',', $type));
		} else {
			$nodeTypes = $this->schemaParserService->parseAll();
		}

		$filename = 'NodeTypes.SchemaOrg.' . $name . '.yaml';

		$this->nodeTypeBuilder
			->setFilename($filename)
			->unlinkExistingFile();

		$success = $error = 0;
		foreach ($nodeTypes as $nodeType) {
			/** @var NodeType $nodeType */
			$this->outputLine("+ <b>" . $nodeType->getName() . "</b>");

			try {
				$existingNodeType = $this->nodeTypeManager->getNodeType($nodeType->getName());
				$this->outputFormatted("   - <b>NodeType \"%s\" skipped</b>, update is not supported ...", array($existingNodeType->getName()));
				++$error;
			} catch (NodeTypeNotFoundException $exception) {
				$filename = $this->nodeTypeBuilder->dump($nodeType);
				++$success;
			}
		}

		$this->outputLine();
		if ($success > 0) {
			$this->outputFormatted("The following file contain your new NodeType: " . $filename);
		} else {
			$this->outputFormatted("Nothing to do ...");
		}

		$this->outputLine();
		$this->outputFormatted("We are on Github, Pull request welcome or open an issue if you have trouble ...");
	}

}