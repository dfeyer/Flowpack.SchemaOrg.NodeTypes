<?php

namespace Flowpack\SchemaOrg\NodeTypes\Command;

/*                                                                               *
 * This script belongs to the Neos Flow package "Flowpack.SchemaOrg.NodeTypes".  *
 *                                                                               */

use Flowpack\SchemaOrg\NodeTypes\Domain\Model\NodeType;
use Flowpack\SchemaOrg\NodeTypes\Service\ConfigurationService;
use Flowpack\SchemaOrg\NodeTypes\Service\NodeTypeBuilder;
use Flowpack\SchemaOrg\NodeTypes\Service\SchemaParserService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\ContentRepository\Exception\NodeTypeNotFoundException;

/**
 * Schema.org Schema Extraction CLI
 */
class SchemaCommandController extends CommandController
{

    /**
     * @Flow\Inject
     * @var SchemaParserService
     */
    protected $schemaParserService;

    /**
     * @Flow\Inject
     * @var NodeTypeManager
     */
    protected $nodeTypeManager;

    /**
     * @Flow\Inject
     * @var NodeTypeBuilder
     */
    protected $nodeTypeBuilder;

    /**
     * @Flow\Inject
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @Flow\Inject(setting="schemas.jsonFilename")
     * @var string
     */
    protected $jsonSchema;

    /**
     * Extract Schema.org to build NodeTypes configuration
     *
     * @param string $name       The name of the file
     * @param string $packageKey The package key
     * @param string $type       The Type from schema.org
     */
    public function extractCommand($name = null, $packageKey = null, $type = null)
    {
        try {
            $name = $name ?: 'Default';
            $this->configurationService->setPackageKey($packageKey);
            $this->outputLine();
            $this->outputFormatted('# Extracting schema.org ...');

            $this->schemaParserService->setAllSchemaJsonFilename($this->jsonSchema);

            if ($type !== null) {
                $nodeTypes = $this->schemaParserService->parseByTypes(explode(',', $type));
            } else {
                $nodeTypes = $this->schemaParserService->parseAll();
            }

            $filename = 'NodeTypes.SchemaOrg.' . $name . '.yaml';

            $this->nodeTypeBuilder
                ->setFilename($filename)
                ->unlinkExistingFile();

            $success = $error = 0;
            foreach ($nodeTypes as $nodeType) {
                /** @var NodeType $nodeType */
                $this->outputLine('+ <b>' . $nodeType->getName() . '</b>');

                try {
                    $existingNodeType = $this->nodeTypeManager->getNodeType($nodeType->getName());
                    $this->outputFormatted('   - <b>NodeType "%s" skipped</b>, update is not supported ...', [$existingNodeType->getName()]);
                    ++$error;
                } catch (NodeTypeNotFoundException $exception) {
                    $filename = $this->nodeTypeBuilder->dump($nodeType);
                    ++$success;
                }
            }

            $this->outputLine();
            if ($success > 0) {
                $this->outputFormatted('The following file contain your new NodeType: ' . $filename);
            } else {
                $this->outputFormatted('Nothing to do ...');
            }

            $this->outputLine();
            $this->outputFormatted('We are on Github, Pull request welcome or open an issue if you have trouble ...');
        } catch (\InvalidArgumentException $exception) {
            $this->outputLine();
            $this->outputFormatted($exception->getMessage());
            $this->sendAndExit(1);
        }
    }
}
