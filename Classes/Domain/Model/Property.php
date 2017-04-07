<?php
namespace Flowpack\SchemaOrg\NodeTypes\Domain\Model;

/*                                                                               *
 * This script belongs to the Neos Flow package "Flowpack.SchemaOrg.NodeTypes".  *
 *                                                                               */

use Flowpack\SchemaOrg\NodeTypes\Service\ConfigurationService;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
use Neos\Utility\Unicode\Functions;

/**
 * Properties
 */
class Property
{

    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @var array
     */
    protected $dataTypeMapping = [
        'Boolean'  => 'boolean',
        'Date'     => 'date',
        'DateTime' => 'date',
        'Float'    => 'float',
        'Number'   => 'integer',
        'Integer'  => 'integer',
        'Text'     => 'string',
        'Time'     => 'date',
        'URL'      => 'string',
    ];

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
     * @var bool
     */
    protected $reloadIfChanged;

    /**
     * @var array
     */
    protected $ui = [];

    /**
     * @var bool
     */
    protected $skipProperty = false;

    /**
     * @param ConfigurationService $configurationService
     * @param string               $type
     * @param string               $name
     * @param string               $label
     * @param string               $comment
     * @param string               $groupName
     * @param bool                 $reloadIfChanged
     *
     */
    public function __construct(ConfigurationService $configurationService, $type, $name, $label, $comment, $groupName, $reloadIfChanged = false)
    {
        $this->configurationService = $configurationService;
        $this->type = $this->convertDataType($type, $name);
        $this->name = (string)$name;
        $this->label = (string)$label;
        $this->comment = (string)$comment;
        $this->ui = Arrays::arrayMergeRecursiveOverrule($this->ui, [
            'label'     => $this->label,
            'comment'   => $this->comment,
            'inspector' => [
                'group' => $groupName,
            ],
        ]);
        $this->reloadIfChanged = (bool)$reloadIfChanged;
    }

    /**
     * @return bool
     */
    public function isSkipProperty()
    {
        return $this->skipProperty;
    }

    /**
     * @param bool $skipProperty
     */
    public function setSkipProperty($skipProperty)
    {
        $this->skipProperty = $skipProperty;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  string $schemaOrgPropertyName
     * @param  string $propertyName
     * @return string
     */
    protected function convertDataType($schemaOrgPropertyName, $propertyName)
    {
        if (strpos($schemaOrgPropertyName, ':')) {
            $type = Functions::substr($propertyName, -1) === 's' ? 'references' : 'reference';
            $schemaOrgPropertyName = $this->configurationService->nodeTypeNameMapping($schemaOrgPropertyName);
            $this->ui = Arrays::setValueByPath($this->ui, 'inspector', [
                'editorOptions' => [
                    'nodeTypes' => [$schemaOrgPropertyName],
                ],
            ]);
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
    public function getConfiguration()
    {
        $configuration = [
            'type' => $this->type,
            'ui'   => $this->ui,
        ];

        return $configuration;
    }
}
