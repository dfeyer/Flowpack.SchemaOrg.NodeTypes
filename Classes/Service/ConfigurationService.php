<?php
namespace Flowpack\SchemaOrg\NodeTypes\Service;

/*                                                                               *
 * This script belongs to the Neos Flow package "Flowpack.SchemaOrg.NodeTypes".  *
 *                                                                               */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;

/**
 * Configuration Service
 * @Flow\Scope("singleton")
 */
class ConfigurationService
{

    /**
     * @Flow\InjectConfiguration("nodeTypeMapping")
     * @var array
     */
    protected $nodeTypeMapping;

    /**
     * @Flow\InjectConfiguration("propertyDefaultConfiguration")
     * @var array
     */
    protected $propertyDefaultConfiguration;

    /**
     * @Flow\InjectConfiguration("typeDefaultConfiguration")
     * @var array
     */
    protected $typeDefaultConfiguration;

    /**
     * @Flow\InjectConfiguration("propertyMixinsMapping")
     * @var array
     */
    protected $propertyMixinsMapping;

    /**
     * @Flow\InjectConfiguration("propertyBlackList")
     * @var array
     */
    protected $propertyBlackList;

    /**
     * @Flow\InjectConfiguration("defaultPackageKey")
     * @var string
     */
    protected $defaultPackageKey;

    /**
     * @var string
     */
    protected $packageKey;

    /**
     * @param  string $nodeTypeName
     * @return string
     */
    public function nodeTypeNameMapping($nodeTypeName)
    {
        list($packageName, $typeName) = explode(':', $nodeTypeName);
        if (isset($this->nodeTypeMapping[$typeName])) {
            return $this->nodeTypeMapping[$typeName];
        }

        return $nodeTypeName;
    }

    /**
     * @param  string $type
     * @return array
     */
    public function getTypeDefaultConfiguration($type)
    {
        $configuration = $this->typeDefaultConfiguration['*'];
        if (isset($this->typeDefaultConfiguration[$type])) {
            $configuration = Arrays::arrayMergeRecursiveOverrule($configuration, $this->typeDefaultConfiguration[$type]);
        }

        return $configuration;
    }

    /**
     * @param  string $propertyName
     * @param  string $nodeTypeName
     * @param  array  $configuration
     * @return array
     * @todo add support for property configuration override for a given $nodeTypeName
     */
    public function mergePropertyConfigurationWithDefaultConfiguration($propertyName, $nodeTypeName, array $configuration)
    {
        $defaultConfiguration = $this->propertyDefaultConfiguration['*'];
        $configuration = Arrays::arrayMergeRecursiveOverrule($defaultConfiguration, $configuration);
        if (isset($this->propertyDefaultConfiguration[$propertyName]) && is_array($this->propertyDefaultConfiguration[$propertyName])) {
            $configuration = Arrays::arrayMergeRecursiveOverrule($configuration, $this->propertyDefaultConfiguration[$propertyName]);
        }

        return $configuration;
    }

    /**
     * @param  string $propertyName
     * @param  string $nodeTypeName
     * @return array
     * @todo add support for property configuration override for a given $nodeTypeName
     */
    public function getNodeTypeMixinsByProperty($propertyName, $nodeTypeName)
    {
        $mixins = [];
        if (isset($this->propertyMixinsMapping[$propertyName]) && is_array($this->propertyMixinsMapping[$propertyName])) {
            $mixins = $this->propertyMixinsMapping[$propertyName];
        }

        return $mixins;
    }

    /**
     * @param  string $propertyName
     * @param  string $nodeTypeName
     * @return boolean
     * @todo add support for property configuration override for a given $nodeTypeName
     */
    public function isPropertyBlacklisted($propertyName, $nodeTypeName)
    {
        if (isset($this->propertyBlackList[$propertyName]) && $this->propertyBlackList[$propertyName] === true) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPackageKey()
    {
        if (trim($this->packageKey) === '') {
            return $this->defaultPackageKey;
        }

        return $this->packageKey;
    }

    /**
     * @param string $packageKey
     */
    public function setPackageKey($packageKey)
    {
        $this->packageKey = $packageKey;
    }
}
