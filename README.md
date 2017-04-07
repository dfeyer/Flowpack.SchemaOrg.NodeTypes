Neos Utility to create NodeType based on schema.org
===================================================

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

What's next ?
-------------

This package can be used for initial content modeling, based on common standard. The current implementation is flexible 
enought for the initial Node Type configuration. But now that you use standard schema configuration, it's maybe time to
work on:

* JSON-LD (JSON Linked Data)
* RDFa
* MicroData

You can check the `Examples` section in the schema.org website: http://schema.org/Person. Maybe some of those template, 
especialy for JSON-LD and RDFa, can also be kickstarted by this package. So if you found this package useful, just open 
issue if you see missing feature, or if you can help (code, documenation, ...) your are welcome.

* TypoScript2 object to render JSON-LD / RDFa, based on the current document/structure content

How import work ?
-----------------

* get the specific schema.org type
* import all `supertypes`
* analysis of relation between schema, `reference` & `references`
* build a dependency tree, and import all related types

So during a single import you can ask for a given type, but that system will import all dependecies required 
by the main type. This is a sample command output when trying to import `UserComments`:

```
# flow schema:extract --type UserComments
# Extracting schema.org ...
+ Flowpack.SchemaOrg.NodeTypes:AdministrativeArea
+ Flowpack.SchemaOrg.NodeTypes:AlignmentObject
+ Flowpack.SchemaOrg.NodeTypes:Article
+ Flowpack.SchemaOrg.NodeTypes:Audience
+ Flowpack.SchemaOrg.NodeTypes:AudioObject
+ Flowpack.SchemaOrg.NodeTypes:BroadcastService
+ Flowpack.SchemaOrg.NodeTypes:BusinessEntityType
+ Flowpack.SchemaOrg.NodeTypes:BusinessFunction
+ Flowpack.SchemaOrg.NodeTypes:ContactPoint
+ Flowpack.SchemaOrg.NodeTypes:ContactPointOption
+ Flowpack.SchemaOrg.NodeTypes:Country
+ Flowpack.SchemaOrg.NodeTypes:CreativeWork
+ Flowpack.SchemaOrg.NodeTypes:DayOfWeek
+ Flowpack.SchemaOrg.NodeTypes:DeliveryMethod
+ Flowpack.SchemaOrg.NodeTypes:Demand
+ Flowpack.SchemaOrg.NodeTypes:Distance
+ Flowpack.SchemaOrg.NodeTypes:Duration
+ Flowpack.SchemaOrg.NodeTypes:EducationalOrganization
+ Flowpack.SchemaOrg.NodeTypes:Enumeration
+ Flowpack.SchemaOrg.NodeTypes:Event
+ Flowpack.SchemaOrg.NodeTypes:EventStatusType
+ Flowpack.SchemaOrg.NodeTypes:GeoCoordinates
+ Flowpack.SchemaOrg.NodeTypes:Intangible
+ Flowpack.SchemaOrg.NodeTypes:ItemAvailability
+ Flowpack.SchemaOrg.NodeTypes:Language
+ Flowpack.SchemaOrg.NodeTypes:MediaObject
+ Flowpack.SchemaOrg.NodeTypes:NewsArticle
+ Flowpack.SchemaOrg.NodeTypes:Offer
+ Flowpack.SchemaOrg.NodeTypes:OfferItemCondition
+ Flowpack.SchemaOrg.NodeTypes:OpeningHoursSpecification
+ Flowpack.SchemaOrg.NodeTypes:Organization
+ Flowpack.SchemaOrg.NodeTypes:OwnershipInfo
+ Flowpack.SchemaOrg.NodeTypes:PaymentMethod
+ Flowpack.SchemaOrg.NodeTypes:Person
+ Flowpack.SchemaOrg.NodeTypes:Place
+ Flowpack.SchemaOrg.NodeTypes:PostalAddress
+ Flowpack.SchemaOrg.NodeTypes:PriceSpecification
+ Flowpack.SchemaOrg.NodeTypes:Product
+ Flowpack.SchemaOrg.NodeTypes:ProductModel
+ Flowpack.SchemaOrg.NodeTypes:PublicationEvent
+ Flowpack.SchemaOrg.NodeTypes:QuantitativeValue
+ Flowpack.SchemaOrg.NodeTypes:Quantity
+ Flowpack.SchemaOrg.NodeTypes:Rating
+ Flowpack.SchemaOrg.NodeTypes:Review
+ Flowpack.SchemaOrg.NodeTypes:StructuredValue
+ Flowpack.SchemaOrg.NodeTypes:Thing
+ Flowpack.SchemaOrg.NodeTypes:TypeAndQuantityNode
+ Flowpack.SchemaOrg.NodeTypes:UserComments
+ Flowpack.SchemaOrg.NodeTypes:UserInteraction
+ Flowpack.SchemaOrg.NodeTypes:VideoObject
+ Flowpack.SchemaOrg.NodeTypes:WarrantyPromise
+ Flowpack.SchemaOrg.NodeTypes:WarrantyScope
The following file contain your new NodeType:
Data/NodeTypes/Configuration/NodeTypes.SchemaOrg.Default.yaml
```

If one of the required type exist on your system, it will be skipped during the import process. Updating Node Type
configuration is not supported by this package and it will be not supported in futur release.

Mixins
------

This package provide some basic mixins, check `NodeTypes.Mixins.yaml`.

Default NodeType Configuration
------------------------------

Feel free to change the default NodeType configuration:

```yaml
Flowpack:
  SchemaOrg:
    NodeTypes:
      typeDefaultConfiguration:
        '*':
          abstract: TRUE
          nodeLabelGenerator: 'Flowpack\SchemaOrg\NodeTypes\Domain\Model\DefaultNodeLabelGenerator'
          ui:
            icon: 'icon-gear'
```

You can also change the default configuration for a specific NodeType:

```yaml
Flowpack:
  SchemaOrg:
    NodeTypes:
      typeDefaultConfiguration:
        'Review':
          abstract: FALSE
          superTypes:
            - 'Neos.NodeTypes:Page'
        'PostalAddress':
          ui:
            icon: 'icon-building'
```

Configuration: NodeType Mapping
-------------------------------

If you have existing NodeType that can replace some schema.org types, check the configuration:

```yaml
Flowpack:
  SchemaOrg:
    NodeTypes:
      nodeTypeMapping:
	  	ImageObject: 'Neos.NodeTypes:Image'
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
		  - 'Neos.NodeTypes:ImageMixin'
		  - 'Neos.NodeTypes:ImageCaptionMixin'
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
		      'Neos.Neos/Validation/EmailAddressValidator': []
```

Fusion prototype generation
---------------------------

As you might want to use the generate schema properties to be included as `JSON-LD` in your template and not 
copy paste all properties into Fusion or Fluid (Fusion preferred of course), we provide a Fusion-Generator
that auto-generates Fusion prototypes for every abstract NodeType that is used in your node.
 
Given you have a nodeType definition and auto-generated the `JobPosting` schema e.g.

```yaml
'My.Site:Job':
  options:
    fusion:
      prototypeGenerator: Flowpack\SchemaOrg\NodeTypes\Domain\Service\SchemaOrgPrototypeGenerator
  superTypes:
    'Neos.NodeTypes:Page': true
    'My.Site:JobPosting': true
```

The prototype generator will generate Fusion objects for all abstract superTypes with name `My.Site:JobPosting.Schema`
that is a `Neos.Fusion:RawArray` containing all properties already.

You can simply use:

```neosfusion
jobPostingMeta = Neos.Fusion:Tag {
    tagName = 'script'
    attributes.type = 'application/ld+json'
    content = My.Site:JobPosting.Schema {
        @process.json = ${Json.stringify(value)}
    }
}
```

to have a full `JSON-LD` in your frontend without manually mapping all properties.
