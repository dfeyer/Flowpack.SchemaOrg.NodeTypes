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
use Symfony\Component\Yaml\Dumper;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Utility\Files;
use TYPO3\Flow\Utility\Now;

/**
 * Neos NodeType builder
 *
 * @Flow\Scope("singleton")
 */
class NodeTypeBuilder {

	/**
	 * @Flow\Inject(setting="renderedNodeTypeRootPath")
	 * @var string
	 */
	protected $renderedNodeTypeRootPath;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @param string $filename
	 * @return $this
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function unlinkExistingFile() {
		Files::unlink($this->getSavePathAndFilename($this->filename));
		return $this;
	}

	/**
	 * @param NodeType $nodeType
	 * @return string
	 */
	public function dump(NodeType $nodeType) {
		$filename = $this->getFilename();
		$dumper = new Dumper();

		$configuration = $nodeType->getDefaultConfiguration();
		if ($nodeType->hasSuperTypes()) {
			if (!isset($configuration['superTypes'])) {
				$configuration['superTypes'] = $nodeType->getSuperTypes();
			} else {
				$configuration['superTypes'] = array_merge($nodeType->getSuperTypes(), $configuration['superTypes']);
			}
		}
		if ($nodeType->hasProperties()) {
			$configuration['properties'] = $nodeType->getProperties();
		}
		$nodeTypeDefinition = array(
			$nodeType->getName() => Arrays::arrayMergeRecursiveOverrule($configuration, $nodeType->getConfiguration())
		);
		$dumper->setIndentation(2);
		$yaml = $dumper->dump($nodeTypeDefinition, 12);

		Files::createDirectoryRecursively($this->renderedNodeTypeRootPath);

		$filename = $this->getSavePathAndFilename($filename);
		file_put_contents($filename, $yaml . chr(10) . chr(10), FILE_APPEND);

		return str_replace(FLOW_PATH_ROOT, '', $filename);
	}

	/**
	 * @return string
	 */
	protected function getFilename() {
		if (trim($this->filename) === '') {
			throw new \InvalidArgumentException("Please set the filename property ...", 1412162107);
		}
		return $this->filename;
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	protected function getSavePathAndFilename($filename) {
		return $this->renderedNodeTypeRootPath . $filename;
	}

}