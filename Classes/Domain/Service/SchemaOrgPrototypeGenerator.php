<?php

namespace Flowpack\SchemaOrg\NodeTypes\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\Neos\Domain\Service\DefaultPrototypeGeneratorInterface;

/**
 * Generate a Fusion prototype for JSON-LD Usage
 *
 * @Flow\Scope("singleton")
 */
class SchemaOrgPrototypeGenerator implements DefaultPrototypeGeneratorInterface
{

    /**
     * @param NodeType $nodeType
     *
     * @return string
     */
    public function generate(NodeType $nodeType)
    {
        if (strpos($nodeType->getName(), ':') === false) {
            return '';
        }
        $output = '';
        /** @var NodeType $superType */
        foreach ($nodeType->getDeclaredSuperTypes() as $superType) {

            if (!$superType->isAbstract()) {
                continue;
            }

            $output .= chr(10);
            $output .= 'prototype(' . $superType->getName() . ') < prototype(Neos.Fusion:RawArray) {' . chr(10);

            $output .= "\t" . '\'@context\' = \'http://schema.org\'' . chr(10);
            list($packageKey, $relativeName) = explode(':', $superType->getName(), 2);
            $output .= "\t" . '\'@type\' = \'' . $relativeName . '\'' . chr(10);

            foreach ($superType->getProperties() as $propertyName => $propertyConfiguration) {
                if (isset($propertyName[0]) && $propertyName[0] !== '_') {
                    $output .= "\t" . $propertyName . ' = ${q(node).property("' . $propertyName . '")}' . chr(10);
                    if (isset($propertyConfiguration['type']) && $propertyConfiguration['type'] === 'DateTime') {
                        $output .= "\t" . $propertyName . '.@process.formatDate = ${Date.format(value, \'Y-m-d\')}' . chr(10);
                    }
                    // todo: handle reference types as nested RawArray
                }
            }

            $output .= '}' . chr(10);

        }
        return $output;
    }


}
