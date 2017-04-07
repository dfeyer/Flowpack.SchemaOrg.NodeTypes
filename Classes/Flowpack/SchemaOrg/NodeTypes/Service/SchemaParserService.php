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
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;

/**
 * Schema.org Parser
 * @Flow\Scope("singleton")
 */
class SchemaParserService {

	/**
	 * @Flow\Inject(lazy=false)
	 * @var ConfigurationService
	 */
	protected $configurationService;

	/**
	 * @Flow\Inject(setting="schemas.jsonFilename")
	 * @var string
	 */
	protected $allSchemaJsonFilename;

	/**
	 * @var array
	 */
	protected $schemas = array();

	/**
	 * @param string $jsonSchemaFilename
	 * @throws \InvalidArgumentException
	 */
	public function setAllSchemaJsonFilename($jsonSchemaFilename) {
		if (!@is_file($jsonSchemaFilename)) {
			throw new \InvalidArgumentException(sprintf('The given file (%s) is not found', $jsonSchemaFilename), 1396190384);
		}
		$this->allSchemaJsonFilename = $jsonSchemaFilename;
		$this->schemas = array();
	}

	/**
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getSchemas() {
		if ($this->schemas !== array()) {
			return $this->schemas;
		}
		$this->schemas = json_decode(file_get_contents($this->allSchemaJsonFilename), TRUE);
		if ($this->schemas === NULL) {
			throw new \InvalidArgumentException('Unable to decode the given json string', 1396168377);
		}
		return $this->schemas;
	}

	/**
	 * @param array|string $path The path to follow. Either a simple array of keys or a string in the format 'foo.bar.baz'
	 * @return mixed
	 */
	public function getSchemaConfigurationByPath($path) {
		$rawSchema = $this->getSchemas();
		return Arrays::getValueByPath($rawSchema, $path);
	}

	/**
	 * @return array
	 */
	public function parseAll() {
		$schemas = array();
		foreach ($this->getSchemaConfigurationByPath('types') as $type => $configuration) {
			$schemas = $this->mergeSchema($schemas, $this->parseByType($type));
		}
		ksort($schemas);
		return $schemas;
	}

	/**
	 * @param array list of schema type to extract, the ancestors schemas will also be extracted
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function parseByTypes(array $types) {
		$schemas = array();
		foreach ($types as $type) {
			$schemas = $this->mergeSchema($schemas, $this->parseByType($type));
		}
		ksort($schemas);
		return $schemas;
	}

	/**
	 * @param string $type
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function parseByType($type) {
		static $parsedTypes = array();

		if (!$type || isset($parsedTypes[$type])) {
			return;
		}

		$parsedTypes[$type] = TRUE;

		$schemas = array();

		$currentRawSchema = $this->getSchemaConfigurationByPath(array('types', $type));
		if ($currentRawSchema === NULL) {
			throw new \InvalidArgumentException(sprintf('The given type (%s) is not found', $type), 1396190989);
		}

		$typeName = $this->getNodeTypeName($type);
		$groupName = strtolower($type);

		$nodeType = new NodeType($typeName, $type);
		$inspector = array(
			'label' => $currentRawSchema['label']
		);
		if ($currentRawSchema['comment']) {
			$inspector['comment'] = $currentRawSchema['comment'];
		}
		$nodeType->setConfigurationByPath('ui', array(
			'inspector' => array(
				'groups' => array(
					$groupName => $inspector
				)
			)
		));

		foreach ($this->parseSuperTypes($type) as $superTypeName => $configuration) {
			$schemas[$superTypeName] = $configuration;
			if (!isset($schemas[$typeName]['superTypes']) || !is_array($schemas[$typeName]['superTypes'])) {
				$schemas[$typeName]['superTypes'] = array();
			}
		}
		$superTypes = $this->getSchemaConfigurationByPath(array('types', $type, 'supertypes'));
		$superType = trim(array_shift($superTypes));
		if ($superType !== '') {
			$nodeType->addSuperType($this->getNodeTypeName($superType));
		}

		$nodeType->setProperties($this->processProperties($currentRawSchema['specific_properties'], $groupName, $nodeType));

		$this->parseRelatedNodeTypes($nodeType, $schemas);

		$schemas[$typeName] = $nodeType;

		return $schemas;
	}

	/**
	 * @param string $dataType
	 * @return boolean
	 */
	public function isSimpleDataType($dataType) {
		return $this->getSchemaConfigurationByPath(array('datatypes', $dataType)) ? TRUE : FALSE;
	}

	/**
	 * @param array $specificProperties
	 * @param string $groupName
	 * @param NodeType $nodeType
	 * @return array
	 * @todo add support for Ranges, when we found a correct solution
	 */
	protected function processProperties(array $specificProperties, $groupName, NodeType $nodeType) {
		$currentProperties = array();
		foreach ($specificProperties as $propertyName) {
			$skipProperty = FALSE;
			$propertyConfiguration = $this->getSchemaConfigurationByPath(array('properties', $propertyName));
			$type = reset($propertyConfiguration['ranges']);

			if ($this->configurationService->isPropertyBlacklisted($propertyName, $type)) {
				continue;
			}

			foreach ($this->configurationService->getNodeTypeMixinsByProperty($propertyName, $type) as $mixin) {
				$nodeType->addSuperType($mixin);
				$skipProperty = TRUE;
			}

			$property = new Property(
				$this->configurationService,
				$this->isSimpleDataType($type) ? $type : $this->getNodeTypeName($type),
				$propertyConfiguration['id'],
				$propertyConfiguration['label'],
				$propertyConfiguration['comment'],
				$groupName,
				FALSE
			);

			$property->setSkipProperty($skipProperty);
			$currentProperties[$propertyName] = $property;
		}
		return $currentProperties;
	}

	/**
	 * @param string $type
	 * @return array
	 */
	protected function parseSuperTypes($type) {
		$schemas = array();
		$superTypes = $this->getSchemaConfigurationByPath(array('types', $type, 'supertypes')) ?: array();
		foreach ($superTypes as $superType) {
			$schemas = $this->mergeSchema($schemas, $this->parseByType($superType));
		}

		return $schemas;
	}

	/**
	 * @param NodeType $nodeType
	 * @param array $schema
	 */
	protected function parseRelatedNodeTypes(NodeType $nodeType, array &$schema) {
		foreach ($nodeType->getRelatedNodeTypes() as $nodeType => $status) {
			$typeName = $this->getSchemaOrgTypeName($nodeType);
			if ($typeName === NULL || isset($schemas[$typeName])) {
				continue;
			}
			$schema = $this->mergeSchema($schema, $this->parseByType($typeName));
		}
	}

	/**
	 * @param array $currentSchemas
	 * @param array|null $additionalSchemas
	 * @return array
	 */
	protected function mergeSchema(array $currentSchemas, $additionalSchemas) {
		if (is_array($additionalSchemas)) {
			$currentSchemas = Arrays::arrayMergeRecursiveOverrule($currentSchemas, $additionalSchemas);
		}
		return $currentSchemas;
	}

	/**
	 * @param string $schemaType
	 * @return string
	 * @throws \InvalidException
	 */
	protected function getNodeTypeName($schemaType) {
		$schemaType = trim($schemaType);
		if ($schemaType === '') {
			throw new \InvalidException("Empty super type name is not allowed", 1412115678);
		}
		return $this->configurationService->getPackageKey() . ':' . $schemaType;
	}

	protected function getSchemaOrgTypeName($nodeType) {
		if (strpos($nodeType, $this->configurationService->getPackageKey()) === FALSE) {
			return NULL;
		}

		return str_replace($this->configurationService->getPackageKey() . ':', '', $nodeType);
	}

}