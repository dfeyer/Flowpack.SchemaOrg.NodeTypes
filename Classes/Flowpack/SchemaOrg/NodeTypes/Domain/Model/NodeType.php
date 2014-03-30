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
 * Schema Model
 */
class NodeType {

	/**
	 * Name of this node type. Example: "TYPO3CR:Folder"
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Configuration for this node type, can be an arbitrarily nested array.
	 *
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * Is this node type marked abstract
	 *
	 * @var boolean
	 */
	protected $abstract = FALSE;

	/**
	 * Is this node type marked final
	 *
	 * @var boolean
	 */
	protected $final = FALSE;

	/**
	 * node types this node type directly inherits from
	 *
	 * @var array
	 */
	protected $superTypes = array();

	/**
	 * @param string $name
	 * @param bool $abstract
	 * @param bool $final
	 */
	function __construct($name, $abstract = FALSE, $final = FALSE) {
		$this->name = (string)$name;
		$this->abstract = (boolean)$abstract;
		$this->final = (boolean)$final;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
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
	 * @param string $superTypeName
	 */
	public function addSuperType($superTypeName) {
		$this->superTypes[$superTypeName] = TRUE;
	}

	/**
	 * @return array
	 */
	public function getSuperTypes() {
		return $this->superTypes;
	}
}