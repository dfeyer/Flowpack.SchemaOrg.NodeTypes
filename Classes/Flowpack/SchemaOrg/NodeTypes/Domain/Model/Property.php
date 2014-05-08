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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;

/**
 * Properties
 */
class Property {

	/**
	 * @var array
	 */
	protected $dataTypeMapping = array(
		'Boolean' => 'boolean',
		'Date' => 'date',
		'DateTime' => 'date',
		'Float' => 'float',
		'Integer' => 'integer',
		'Number' => 'integer',
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
	 * @param string $type
	 * @param string $name
	 * @param string $label
	 * @param string $comment
	 * @param string $groupName
	 * @param boolean $reloadIfChanged
	 *
	 */
	public function __construct($type, $name, $label, $comment, $groupName, $reloadIfChanged = FALSE) {
		$this->type = $this->convertDataType($type);
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
	 * @param string $schemaOrgPropertyName
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	protected function convertDataType($schemaOrgPropertyName) {
		if (strpos($schemaOrgPropertyName, ':')) {
			$type = 'reference';
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
}