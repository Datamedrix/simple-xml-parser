<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of an application or library developed by Datamedrix and
 * is subject to the provisions of your License Agreement with
 * Datamedrix GmbH.
 *
 * @copyright (c) 2018 Datamedrix GmbH
 * ----------------------------------------------------------------------------
 * @author Christian Graf <c.graf@datamedrix.com>
 */

declare(strict_types=1);

namespace DMX\PHPUnit\Framework\Tests\Unit;

use DMX\SimpleXML\Parser;
use PHPUnit\Framework\TestCase;
use DMX\SimpleXML\Tests\Mocks\ParserMock;

class ParserTest extends TestCase
{
    /**
     * @var array
     */
    private $testArray = [
        '@attributes' => [
            'version' => 'test',
        ],
        'element_a' => [
            '@attributes' => [
                'name' => 'foo',
                'bar' => 1,
            ],
            'element_b' => [
                'element_c' => [
                    0 => [
                        '@attributes' => [
                            'name' => 'bar',
                            'foo' => 0,
                        ],
                        'value1' => '-100',
                        'value2' => 'jadda jdadda',
                    ],
                    1 => [
                        'value1' => '-200',
                        'value2' => 'bla bla bla bla bla 42 bla',
                    ],
                    2 => [
                        'value1' => '23',
                        'value2' => 'text',
                        'more' => [
                            0 => [
                                'name' => 'test 1',
                            ],
                            1 => [
                                'name' => 'test 2',
                            ],
                            2 => [
                                '@attributes' => [
                                    'test' => 'true',
                                ],
                                'name' => 'test 3',
                            ],
                        ],
                    ],
                    3 => [
                        'value1' => '0',
                    ],
                ],
            ],
        ],
    ];

    /**
     * @var array
     */
    private $expectedArray = [
        '@attributes' => [
            'version' => 'test',
        ],
        'element_a' => [
            '@attributes' => [
                'name' => 'foo',
                'bar' => 1,
            ],
            'element_b' => [
                '@attributes' => [],
                'element_c' => [
                    '@attributes' => [],
                    0 => [
                        '@attributes' => [
                            'name' => 'bar',
                            'foo' => 0,
                        ],
                        'value1' => '-100',
                        'value2' => 'jadda jdadda',
                    ],
                    1 => [
                        '@attributes' => [],
                        'value1' => '-200',
                        'value2' => 'bla bla bla bla bla 42 bla',
                    ],
                    2 => [
                        '@attributes' => [],
                        'value1' => '23',
                        'value2' => 'text',
                        'more' => [
                            '@attributes' => [],
                            0 => [
                                '@attributes' => [],
                                'name' => 'test 1',
                            ],
                            1 => [
                                '@attributes' => [],
                                'name' => 'test 2',
                            ],
                            2 => [
                                '@attributes' => ['test' => 'true'],
                                'name' => 'test 3',
                            ],
                        ],
                    ],
                    3 => [
                        '@attributes' => [],
                        'value1' => '0',
                    ],
                ],
            ],
        ],
    ];

    /**
     * @var string
     */
    private $testXML = <<<XML
<xml version="test">
    <element_a name="foo" bar="1">
        <element_b>
            <element_c name="bar" foo="0">
                <value1>-100</value1>
                <value2>jadda jdadda</value2>
            </element_c>
            <element_c>
                <value1>-200</value1>
                <value2>bla bla bla bla bla 42 bla</value2>
            </element_c>
            <element_c>
                <value1>23</value1>
                <value2>text</value2>
                <more><name>test 1</name></more>
                <more><name>test 2</name></more>
                <more test="true"><name>test 3</name></more>
            </element_c>
            <element_c>
                <value1>0</value1>
                <value2></value2>
            </element_c>
        </element_b>
    </element_a>
</xml>
XML;

    /**
     * Test.
     */
    public function testConstructor()
    {
        $dummyContent = '<xml><foo><bar><random test="TestCase">' . rand(-100, 100) . '</random></bar></foo></xml>';
        $parser = new Parser($dummyContent);

        $this->assertAttributeNotEmpty('xmlContent', $parser);
        $this->assertAttributeEquals($dummyContent, 'xmlContent', $parser);

        $this->assertAttributeEquals(false, 'validateDTD', $parser);
        $this->assertAttributeEquals(null, 'xml', $parser);
        $this->assertAttributeEquals(null, 'arrayContent', $parser);
    }

    /**
     * Test.
     */
    public function testValidateDTD()
    {
        $parser = new Parser('<xml><foo bar="true"/></xml>');

        $this->assertFalse($parser->validateDTD());
        $this->assertTrue($parser->validateDTD(true));

        $this->assertAttributeEquals(true, 'validateDTD', $parser);
    }

    /**
     * Test.
     */
    public function testSimpleXMLOptions()
    {
        $parser = new ParserMock();

        $this->assertEquals(LIBXML_DTDATTR | LIBXML_NOBLANKS, $parser->callSimpleXMLOptions());
        $parser->validateDTD(true);
        $this->assertEquals(LIBXML_DTDATTR | LIBXML_NOBLANKS | LIBXML_DTDVALID, $parser->callSimpleXMLOptions());
    }

    /**
     * Test.
     */
    public function testSimpleXMLElement()
    {
        $parser = new ParserMock();

        $xml = $parser->callSimpleXMLElement();

        $this->assertAttributeEquals($xml, 'xml', $parser);
        $this->assertEquals($xml, $parser->callSimpleXMLElement());
    }

    /**
     * Test.
     */
    public function testNormalizeArray()
    {
        $parser = new ParserMock();

        $this->assertEquals($this->expectedArray, $parser->callNormalizeArray($this->testArray));
        $this->assertEquals($this->expectedArray, $parser->callNormalizeArray($this->expectedArray));
    }

    /**
     * Test.
     */
    public function testToArray()
    {
        $parser = new Parser($this->testXML);

        $this->assertEquals($this->expectedArray, $parser->toArray());
    }

    /**
     * Test.
     */
    public function testToJson()
    {
        $parser = new Parser($this->testXML);

        $this->assertEquals(json_encode($parser->toArray()), $parser->toJson());
    }
}
