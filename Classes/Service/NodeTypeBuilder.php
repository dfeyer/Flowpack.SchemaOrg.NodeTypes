<?php
namespace Flowpack\SchemaOrg\NodeTypes\Service;

/*                                                                               *
 * This script belongs to the Neos Flow package "Flowpack.SchemaOrg.NodeTypes".  *
 *                                                                               */

use Flowpack\SchemaOrg\NodeTypes\Domain\Model\NodeType;
use Symfony\Component\Yaml\Dumper;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
use Neos\Utility\Files;

/**
 * Neos NodeType builder
 *
 * @Flow\Scope("singleton")
 */
class NodeTypeBuilder
{

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
     * @param  string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return $this
     */
    public function unlinkExistingFile()
    {
        Files::unlink($this->getSavePathAndFilename($this->filename));
        return $this;
    }

    /**
     * @param  NodeType $nodeType
     * @return string
     */
    public function dump(NodeType $nodeType)
    {
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
        $nodeTypeDefinition = [
            $nodeType->getName() => Arrays::arrayMergeRecursiveOverrule($configuration, $nodeType->getConfiguration()),
        ];
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
    protected function getFilename()
    {
        if (trim($this->filename) === '') {
            throw new \InvalidArgumentException('Please set the filename property ...', 1412162107);
        }
        return $this->filename;
    }

    /**
     * @param  string $filename
     * @return string
     */
    protected function getSavePathAndFilename($filename)
    {
        return $this->renderedNodeTypeRootPath . $filename;
    }
}
