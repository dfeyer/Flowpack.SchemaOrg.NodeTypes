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
 * Schema.org Parser
 *
 * @Flow\Scope("singleton")
 */
class SchemaParserService {

	/**
	 * @var SchemaDefinitions
	 */
	protected $schemaDefinitions;

	/**
	 * @var array
	 */
	protected $schemaCache = array();

	/**
	 * @param SchemaDefinitions $schemaDefinitions
	 * @return array
	 */
	public function parse(SchemaDefinitions $schemaDefinitions) {
		$this->schemaDefinitions = $schemaDefinitions;
		$schemas = array();
		foreach ($this->schemaDefinitions->getTypes() as $typeName => $configuration) {
			$schemas = Arrays::arrayMergeRecursiveOverrule($schemas, $this->parseByType($typeName));
		}
		return $schemas;
	}

	/**
	 * @param string $schemaTypeName
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	protected function parseByType($schemaTypeName) {
		if (isset($this->schemaCache[$schemaTypeName])) {
			return $this->schemaCache[$schemaTypeName];
		}
		$schemas = array();

		$currentRawSchema = $this->schemaDefinitions->getTypesByName($schemaTypeName);
		if ($currentRawSchema === NULL) {
			throw new \InvalidArgumentException(sprintf('The given type (%s) is not found', $schemaTypeName), 1396190989);
		}

		$nodeTypeName = $this->getNodeTypeName($schemaTypeName);
		$groupName = strtolower($schemaTypeName);

		$nodeType = new NodeType($nodeTypeName, TRUE);
		$nodeType->setConfigurationByPath('ui', array(
			'inspector' => array(
				'groups' => array(
					$groupName => array(
						'label' => $currentRawSchema['label'],
						'comment' => $currentRawSchema['comment'] ?: NULL
					)
				)
			)
		));
		$nodeType->setConfigurationByPath('properties', $this->processProperties($currentRawSchema, $groupName));

		foreach ($currentRawSchema['ancestors'] as $ancestorName) {
			$nodeType->addAncestor($this->getNodeTypeName($ancestorName));
		}

		foreach ($currentRawSchema['supertypes'] as $superTypeName) {
			$nodeType->addSuperType($this->getNodeTypeName($superTypeName));
		}

		$schemas[$nodeTypeName] = $nodeType;
		$this->schemaCache[$schemaTypeName] = $schemas;

		return $schemas;
	}

	/**
	 * @param array $properties
	 * @param string $groupName
	 * @return array
	 * @todo add support for Ranges, when we found a correct solution
	 * @throws \TYPO3\Flow\Exception
	 */
	protected function processProperties(array $properties, $groupName) {
		if (!is_array($properties['specific_properties'])) {
			throw new Exception('Specific properties must be an array', 1398204504);
		}
		$currentProperties = array();
		foreach ($properties['specific_properties'] as $propertyName) {
			$propertyConfiguration = $this->schemaDefinitions->getByPath(array('properties', $propertyName));
			$type = reset($propertyConfiguration['ranges']);

			$currentProperties[$propertyName] = new Property(
				$this->schemaDefinitions->isSimpleDataType($type) ? $type : $this->getNodeTypeName($type),
				$propertyConfiguration['id'],
				$propertyConfiguration['label'],
				$propertyConfiguration['comment'],
				$groupName,
				FALSE
			);
		}
		return $currentProperties;
	}

	/**
	 * @param string $schemaType
	 * @return string
	 */
	protected function getNodeTypeName($schemaType) {
		return 'Flowpack.SchemaOrg.NodeTypes:' . $schemaType;
	}

}