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
use TYPO3\TYPO3CR\Domain\Model\AbstractNodeData;
use TYPO3\TYPO3CR\Domain\Model\NodeLabelGeneratorInterface;

/**
 * The default node label generator; used if no-other is configured
 *
 * @Flow\Scope("singleton")
 */
class DefaultNodeLabelGenerator implements NodeLabelGeneratorInterface {

	/**
	 * Render a node label
	 *
	 * @param AbstractNodeData $nodeData
	 * @param boolean $crop
	 * @return string
	 */
	public function getLabel(AbstractNodeData $nodeData, $crop = TRUE) {
		if ($nodeData->hasProperty('name') === TRUE && $nodeData->getProperty('name') !== '') {
			$label = strip_tags($nodeData->getProperty('name'));
		} else {
			$label = ($nodeData->getNodeType()->getLabel() ?: $nodeData->getNodeType()->getName()) . ' (' . $nodeData->getName() . ')';
		}

		return $label;
	}
}
