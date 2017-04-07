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
use Neos\Flow\Utility\Unicode\Functions;

/**
 * Properties
 */
class Property {

	/**
	 * @var ConfigurationService
	 */
	protected $configurationService;

	/**
	 * @var array
	 */
	protected $dataTypeMapping = array(
		'Boolean' => 'boolean',
		'Date' => 'date',
		'DateTime' => 'date',
		'Float' => 'float',
		'Number' => 'integer',
		'Integer' => 'integer',
		'Text' => 'string',
		'Time' => 'date',
		'URL' => 'string'
	);

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $comment;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var boolean
	 */
	protected $reloadIfChanged;

	/**
	 * @var array
	 */
	protected $ui = array();

	/**
	 * @var boolean
	 */
	protected $skipProperty = FALSE;

	/**
	 * @param ConfigurationService $configurationService
	 * @param string $type
	 * @param string $name
	 * @param string $label
	 * @param string $comment
	 * @param string $groupName
	 * @param boolean $reloadIfChanged
	 *
	 */
	public function __construct(ConfigurationService $configurationService, $type, $name, $label, $comment, $groupName, $reloadIfChanged = FALSE) {
		$this->configurationService = $configurationService;
		$this->type = $this->convertDataType($type, $name);
		$this->name = (string)$name;
		$this->label = (string)$label;
		$this->comment = (string)$comment;
		$this->ui = Arrays::arrayMergeRecursiveOverrule($this->ui, array(
			'label' => $this->label,
			'comment' => $this->comment,
			'inspector' => array(
				'group' => $groupName
			)
		));
		$this->reloadIfChanged = (boolean)$reloadIfChanged;
	}

	/**
	 * @return boolean
	 */
	public function isSkipProperty() {
		return $this->skipProperty;
	}

	/**
	 * @param boolean $skipProperty
	 */
	public function setSkipProperty($skipProperty) {
		$this->skipProperty = $skipProperty;
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
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $schemaOrgPropertyName
	 * @param string $propertyName
	 * @return string
	 */
	protected function convertDataType($schemaOrgPropertyName, $propertyName) {
		if (strpos($schemaOrgPropertyName, ':')) {
			$type = Functions::substr($propertyName, -1) === 's' ? 'references' : 'reference';
			$schemaOrgPropertyName = $this->configurationService->nodeTypeNameMapping($schemaOrgPropertyName);
			$this->ui = Arrays::setValueByPath($this->ui, 'inspector', array(
				'editorOptions' => array(
					'nodeTypes' => array($schemaOrgPropertyName)
				)
			));
		} else {
			if (!isset($this->dataTypeMapping[$schemaOrgPropertyName]) && strpos($schemaOrgPropertyName, ':') > 0) {
				throw new \InvalidArgumentException(sprintf('Invalid property type (%s)', $schemaOrgPropertyName), 1396192757);
			}
			$type = (string)$this->dataTypeMapping[$schemaOrgPropertyName];
		}
		return $type;
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		$configuration = array(
			'type' => $this->type,
			'ui' => $this->ui
		);

		return $configuration;
	}
}