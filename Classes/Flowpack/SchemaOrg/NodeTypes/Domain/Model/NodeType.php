<?php
namespace Flowpack\SchemaOrg\NodeTypes\Domain\Model;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SchemaOrg.NodeTypes". *
 *                                                                               *
 * It is free software; you can redistribute it and/or modify it under           *
 * the terms of the GNU Lesser General Public License, either version 3          *
 * of the License, or (at your option) any later version.                        *
 *                                                                               *
 * The TYPO3 project - inspiring people to share!                                *
 *                                                                               */

use Flowpack\SchemaOrg\NodeTypes\Service\ConfigurationService;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;

/**
 * NodeType Definition
 */
class NodeType {

	/**
	 * @Flow\Inject
	 * @var ConfigurationService
	 */
	protected $configurationService;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var array
	 */
	protected $superTypes = array();

	/**
	 * @var array
	 */
	protected $properties = array();

	/**
	 * @var array
	 */
	protected $relatedNodeTypes = array();

	/**
	 * @param string $name
	 * @param string $type
	 */
	function __construct($name, $type) {
		$this->name = (string)$name;
		$this->type = (string)$type;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		list(, $label) = explode(':', $this->getName());
		return $label;
	}

	/**
	 * @return boolean
	 */
	public function getAbstract() {
		return $this->abstract;
	}

	/**
	 * @return boolean
	 */
	public function getFinal() {
		return $this->final;
	}

	/**
	 * @return array
	 */
	public function getDefaultConfiguration() {
		return Arrays::arrayMergeRecursiveOverrule($this->configurationService->getTypeDefaultConfiguration($this->type), array(
			'ui' => array(
				'label' => $this->getLabel(),
			)
		));
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * @param array|string $path The path to follow. Either a simple array of keys or a string in the format 'foo.bar.baz'
	 * @param mixed $value
	 */
	public function setConfigurationByPath($path, $value) {
		$this->configuration = Arrays::setValueByPath($this->configuration, $path, $value);
	}

	/**
	 * @param array|string $path The path to follow. Either a simple array of keys or a string in the format 'foo.bar.baz'
	 * @param mixed $value
	 */
	public function getConfigurationByPath($path) {
		$this->configuration = Arrays::getValueByPath($this->configuration, $path);
	}

	/**
	 * @param string $superTypeName
	 */
	public function addSuperType($superTypeName) {
		$this->superTypes[$superTypeName] = TRUE;
	}

	/**
	 * @return array
	 */
	public function getSuperTypes() {
		$superTypes = array();
		foreach ($this->superTypes as $superTypeName=>$superTypeStatus) {
			if ($superTypeStatus === TRUE) {
				$superTypes[] = $superTypeName;
			}
		}

		return $superTypes;
	}

	/**
	 * @return boolean
	 */
	public function hasSuperTypes() {
		return (boolean)count($this->getSuperTypes());
	}

	/**
	 * @return array
	 */
	public function getRelatedNodeTypes() {
		return $this->relatedNodeTypes;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * @param array $properties
	 */
	public function setProperties($properties) {
		$this->properties = array();
		foreach ($properties as $property) {
			$this->initializeRelatedNodeTypes($property);
			/** @var Property $property */
			if ($property->isSkipProperty()) {
				continue;
			}
			$propertyName = $property->getName();
			$configuration = $property->getConfiguration();

			$this->properties[$propertyName] = $this->configurationService->mergePropertyConfigurationWithDefaultConfiguration($propertyName, $this->getName(), $configuration);
		}
	}

	/**
	 * @return boolean
	 */
	public function hasProperties() {
		return (boolean)count($this->getProperties());
	}

	/**
	 * @param Property $property
	 * @return void
	 */
	protected function initializeRelatedNodeTypes(Property $property) {
		if (substr($property->getType(), 0, 9) !== 'reference') {
			return;
		}

		$configuration = $property->getConfiguration();
		$nodeTypes = Arrays::getValueByPath($configuration, 'ui.inspector.editorOptions.nodeTypes') ?: array();
		foreach ($nodeTypes as $nodeType) {
			$this->relatedNodeTypes[$nodeType] = TRUE;
		}
	}

}