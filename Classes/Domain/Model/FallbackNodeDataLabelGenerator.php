<?php
namespace Flowpack\SchemaOrg\NodeTypes\Domain\Model;

/*                                                                               *
 * This script belongs to the Neos Flow package "Flowpack.SchemaOrg.NodeTypes".  *
 *                                                                               */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Model\NodeLabelGeneratorInterface;

/**
 * The default node label generator; used if no-other is configured
 *
 * @Flow\Scope("singleton")
 */
class FallbackNodeDataLabelGenerator implements NodeLabelGeneratorInterface
{

    /**
     * Render a node label
     *
     * @param  NodeInterface $node
     * @param  bool          $crop
     * @return string
     */
    public function getLabel(NodeInterface $node, $crop = true)
    {
        if ($node->hasProperty('name') === true && $node->getProperty('name') !== '') {
            $label = strip_tags($node->getProperty('name'));
        } else {
            $label = ($node->getNodeType()->getLabel() ?: $node->getNodeType()->getName()) . ' (' . $node->getName() . ')';
        }

        return $label;
    }
}
