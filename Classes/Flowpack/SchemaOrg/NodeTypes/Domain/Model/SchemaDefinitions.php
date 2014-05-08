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
 * SchemaDefinitions
 */
class SchemaDefinitions {

	/**
	 * @Flow\Inject(setting="schemas.source")
	 * @var string
	 */
	protected $source;

	/**
	 * @var array
	 */
	protected $schemaDefintions = array();

	/**
	 * @param string $source
	 */
	public function setSource($source) {
		$this->source = $source;
	}

	/**
	 * @return $this
	 */
	public function initialize() {
		if ($this->schemaDefintions !== array()) {
			return $this;
		}
		$this->schemaDefintions = json_decode(file_get_contents($this->source), TRUE);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getDefinitions() {
		$this->initialize();
		return $this->schemaDefintions;
	}

	/**
	 * @param string $dataType
	 * @return boolean
	 */
	public function isSimpleDataType($dataType) {
		return $this->getByPath(array('datatypes', $dataType)) ? TRUE : FALSE;
	}

	/**
	 * @return array
	 */
	public function getTypes() {
		return $this->getByPath('types');
	}

	/**
	 * @param string $schemaTypeName
	 * @return mixed
	 */
	public function getTypesByName($schemaTypeName) {
		return $this->getByPath(array('types', $schemaTypeName));
	}

	/**
	 * @param string $path
	 * @return mixed
	 */
	public function getByPath($path) {
		$this->initialize();
		return Arrays::getValueByPath($this->schemaDefintions, $path);
	}

}