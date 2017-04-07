<?php
namespace Neos\Neos\Tests\Unit\Service;

/*                                                                               *
 * This script belongs to the Neos Flow package "Flowpack.SchemaOrg.NodeTypes".  *
 *                                                                               */

use Flowpack\SchemaOrg\NodeTypes\Service\SchemaParserService;
use Neos\Utility\ObjectAccess;
use Neos\Flow\Tests\UnitTestCase;

/**
 * Testcase for the HTML Augmenter
 *
 */
class SchemaParserServiceTest extends UnitTestCase
{

    /**
     * @return SchemaParserService
     */
    protected function createParser()
    {
        parent::setUp();
        $parser = new SchemaParserService();
        $parser->setAllSchemaJsonFilename(__DIR__ . '/Fixtures/Minimal.json');
        return $parser;
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1396190384
     */
    public function setAllSchemaJsonFilenameThrowAnExceptionIfTheFileIsNotFound()
    {
        $parser = $this->createParser();
        $parser->setAllSchemaJsonFilename(__DIR__ . '/Fixtures/NotFound.json');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1396168377
     */
    public function getSchemasReturnAnExceptionIfTheGivenJsonFileIsNotValid()
    {
        $parser = $this->createParser();
        $parser->setAllSchemaJsonFilename(__DIR__ . '/Fixtures/Invalid.json');
        $parser->getSchemas();
    }

    /**
     * @test
     */
    public function setAllSchemaJsonFilenameResetTheProcessedSchemas()
    {
        $parser = $this->createParser();
        $parser->parseByTypes(['Thing']);
        $currentSchemas = $parser->getSchemas();
        $parser->setAllSchemaJsonFilename(__DIR__ . '/Fixtures/Empty.json');
        $this->assertSame(__DIR__ . '/Fixtures/Empty.json', ObjectAccess::getProperty($parser, 'allSchemaJsonFilename', true));
        $this->assertNotSame($currentSchemas, $parser->getSchemas());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1396190989
     */
    public function parseByTypeThrowAnExceptionIfTheTypeIsNotFound()
    {
        $parser = $this->createParser();
        $parser->parseByTypes(['Foo']);
    }

    /**
     * @test
     */
    public function parseByTypesReturnTheGivenSchemaWithAllSuperTypes()
    {
        $parser = $this->createParser();
        $nodeTypes = $parser->parseByTypes(['Person']);

        $this->assertTrue(count($nodeTypes) === 2);
        $keys = array_keys($nodeTypes);

        $this->assertSame('Flowpack.SchemaOrg.NodeTypes:Thing', $keys[0]);
        $this->assertSame('Flowpack.SchemaOrg.NodeTypes:Person', $keys[1]);
    }

    /**
     * @test
     */
    public function parseAllReturnAllAvailableNodeType()
    {
        $parser = $this->createParser();
        $nodeTypes = $parser->parseAll();
        $this->assertSame(548, count($nodeTypes));
    }
}
