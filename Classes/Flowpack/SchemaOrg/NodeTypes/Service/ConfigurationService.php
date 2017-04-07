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

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;

/**
 * Configuration Service
 * @Flow\Scope("singleton")
 */
class ConfigurationService {

	/**
	 * @Flow\Inject(setting="nodeTypeMapping")
	 * @var string
	 */
	protected $nodeTypeMapping;

	/**
	 * @Flow\Inject(setting="propertyDefaultConfiguration")
	 * @var string
	 */
	protected $propertyDefaultConfiguration;

	/**
	 * @Flow\Inject(setting="typeDefaultConfiguration")
	 * @var string
	 */
	protected $typeDefaultConfiguration;

	/**
	 * @Flow\Inject(setting="propertyMixinsMapping")
	 * @var string
	 */
	protected $propertyMixinsMapping;

	/**
	 * @Flow\Inject(setting="propertyBlackList")
	 * @var string
	 */
	protected $propertyBlackList;

	/**
	 * @Flow\Inject(setting="defaultPackageKey")
	 * @var string
	 */
	protected $defaultPackageKey;

	/**
	 * @var string
	 */
	protected $packageKey;

	/**
	 * @param string $nodeTypeName
	 * @return string
	 */
	public function nodeTypeNameMapping($nodeTypeName) {
		list($packageName, $typeName) = explode(':', $nodeTypeName);
		if (isset($this->nodeTypeMapping[$typeName])) {
			return $this->nodeTypeMapping[$typeName];
		}

		return $nodeTypeName;
	}

	/**
	 * @param string $type
	 * @return array
	 */
	public function getTypeDefaultConfiguration($type) {
		$configuration = $this->typeDefaultConfiguration['*'];
		if (isset($this->typeDefaultConfiguration[$type])) {
			$configuration = Arrays::arrayMergeRecursiveOverrule($configuration, $this->typeDefaultConfiguration[$type]);
		}

		return $configuration;
	}

	/**
	 * @param string $propertyName
	 * @param string $nodeTypeName
	 * @param array $configuration
	 * @return array
	 * @todo add support for property configuration override for a given $nodeTypeName
	 */
	public function mergePropertyConfigurationWithDefaultConfiguration($propertyName, $nodeTypeName, array $configuration) {
		$defaultConfiguration = $this->propertyDefaultConfiguration['*'];
		$configuration = Arrays::arrayMergeRecursiveOverrule($defaultConfiguration, $configuration);
		if (isset($this->propertyDefaultConfiguration[$propertyName]) && is_array($this->propertyDefaultConfiguration[$propertyName])) {
			$configuration = Arrays::arrayMergeRecursiveOverrule($configuration, $this->propertyDefaultConfiguration[$propertyName]);
		}

		return $configuration;
	}

	/**
	 * @param string $propertyName
	 * @param string $nodeTypeName
	 * @return array
	 * @todo add support for property configuration override for a given $nodeTypeName
	 */
	public function getNodeTypeMixinsByProperty($propertyName, $nodeTypeName) {
		$mixins = array();
		if (isset($this->propertyMixinsMapping[$propertyName]) && is_array($this->propertyMixinsMapping[$propertyName])) {
			$mixins = $this->propertyMixinsMapping[$propertyName];
		}

		return $mixins;
	}

	/**
	 * @param string $propertyName
	 * @param string $nodeTypeName
	 * @return array
	 * @todo add support for property configuration override for a given $nodeTypeName
	 */
	public function isPropertyBlacklisted($propertyName, $nodeTypeName) {
		if (isset($this->propertyBlackList[$propertyName]) && $this->propertyBlackList[$propertyName] === TRUE) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return string
	 */
	public function getPackageKey() {
		if (trim($this->packageKey) === '') {
			return $this->defaultPackageKey;
		}

		return $this->packageKey;
	}

	/**
	 * @param string $packageKey
	 */
	public function setPackageKey($packageKey) {
		$this->packageKey = $packageKey;
	}

}