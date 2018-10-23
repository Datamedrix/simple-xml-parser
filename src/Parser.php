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

namespace DMX\SimpleXML;

use SimpleXMLElement;

class Parser
{
    /**
     * @var string
     */
    protected $xmlContent;

    /**
     * @var bool
     */
    protected $validateDTD = false;

    /**
     * @var null|SimpleXMLElement
     */
    private $xml = null;

    /**
     * @var null|array
     */
    private $arrayContent = null;

    /**
     * Parser constructor.
     *
     * @param string $xmlContent
     */
    public function __construct(string $xmlContent)
    {
        $this->xmlContent = trim($xmlContent);
    }

    /**
     * @param bool|null $validate
     *
     * @return bool
     */
    public function validateDTD(?bool $validate = null): bool
    {
        if ($validate !== null) {
            $this->validateDTD = $validate;
        }

        return $this->validateDTD;
    }

    /**
     * @return int
     */
    protected function simpleXMLOptions(): int
    {
        $options = LIBXML_DTDATTR | LIBXML_NOBLANKS;
        if ($this->validateDTD) {
            $options = $options | LIBXML_DTDVALID;
        }

        return $options;
    }

    /**
     * @return SimpleXMLElement
     */
    protected function simpleXMLElement(): SimpleXMLElement
    {
        if ($this->xml === null) {
            $this->xml = new SimpleXMLElement($this->xmlContent, $this->simpleXMLOptions(), false);

            // remove empty tags
            $xpath = '//*[not(normalize-space())]';
            foreach (array_reverse($this->xml->xpath($xpath)) as $remove) {
                unset($remove[0]);
            }
        }

        return $this->xml;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function normalizeArray(array $array): array
    {
        if (!isset($array['@attributes'])) {
            $array['@attributes'] = [];
        }

        foreach ($array as $i => $item) {
            if ($i === '@attributes') {
                continue;
            }

            if (is_array($item)) {
                $array[$i] = $this->normalizeArray($item);
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if ($this->arrayContent === null) {
            $json = json_encode($this->simpleXMLElement());
            $schema = json_decode($json, true);

            $schema = $this->normalizeArray($schema);
            $this->arrayContent = $schema;
        }

        return $this->arrayContent;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
