TYPO3 Neos Utility to create NodeType based on schema.org
=========================================================

This plugin provides a service to create NodeType YAML configuration based on schema.org
type. You can found the full schema.org types list here: http://schema.org/docs/full.html

Quick start
-----------
* install this package with composer

* use the command line tools to create the YAML configuration

* you can import only a subset of the types available, type type attribute can contain a list
separated by comma of valid Schema.org type. The following command will create a file named 
`NodeTypes.SchemaOrg.APIReference.yaml` in your `Data/NodeTypes/Configuration` directory.

```
# flow schema:extract --package-key Flowpack.SchemaOrg.NodeTypes --name APIReference --type APIReference
```

* you can also import the complete list of types (around ~540 types currently). The following command will 
create a file named `NodeTypes.SchemaOrg.All.yaml` in your `Data/NodeTypes/Configuration` directory.

```
# flow schema:extract --package-key Flowpack.SchemaOrg.NodeTypes --name All
```

* all create type are abstract, you need to use them is your own NodeType.

* copy this file to your own package and use your newly created schemas

Mixins
------

This package provide some basic mixins, check `NodeTypes.Mixins.yaml`.

Configuration: NodeType Mapping
-------------------------------

If you have existing NodeType that can replace some schema.org types, check the configuration:

```yaml
Flowpack:
  SchemaOrg:
    NodeTypes:
      nodeTypeMapping:
	  	ImageObject: 'TYPO3.Neos.NodeTypes:Image'
```

Configuration: Property Blacklist
---------------------------------

If you have existing NodeType that can replace some schema.org types, check the configuration:

```yaml
Flowpack:
  SchemaOrg:
    NodeTypes:
      propertyBlackList:
        'url': TRUE
```

Configuration: Replace a property by a mixin
--------------------------------------------

Sometimes it's useful to replace a property by a existing mixins, per example to use a property as a child nodes:


```yaml
Flowpack:
  SchemaOrg:
    NodeTypes:
      propertyMixinsMapping:
	    'image':
		  - 'TYPO3.Neos.NodeTypes:ImageMixin'
		  - 'TYPO3.Neos.NodeTypes:ImageCaptionMixin'
```

Configuration: Override property configuration
----------------------------------------------

Sometimes it's useful to override the default property configuration. This package try to be smart, but you are smarter
so feel free to change configuration for a give property.

```yaml
Flowpack:
  SchemaOrg:
    NodeTypes:
      propertyDefaultConfiguration:
	    'email':
		  type: 'string'
		  validation:
		    validation:
		      'TYPO3.Neos/Validation/EmailAddressValidator': []
```