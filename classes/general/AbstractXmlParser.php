<?php

abstract class AbstractXmlParser
{

    protected string $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $path
     * @return DOMXPath
     * @throws Exception
     */
    protected function getDomXml(string $path): DOMXPath
    {
        $xmlString = $this->readXml($path);
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        if (!@$dom->loadXML($xmlString)) {
            throw new Exception("Некорректный XML файл");
        }

        return new DOMXPath($dom);
    }

    /**
     * @param string $filePath
     * @return string
     * @throws Exception
     */
    protected function readXml(string $filePath): string
    {
        if (!is_readable($filePath)) {
            throw new Exception("Файл недоступен: {$filePath}");
        }

        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new Exception("Ошибка чтения XML файла: {$filePath}");
        }

        return $data;
    }

    /**
     * @param DOMNode $node
     * @return array|array[]
     */
    public function allXmlToArray(DOMNode $node): array
    {
        $output = [];

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $output['@attributes'][$attr->nodeName] = $attr->nodeValue;
            }
        }

        if ($node->hasChildNodes()) {
            $textOnly = true;
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                    $text = trim($child->nodeValue);
                    if ($text !== '') {
                        if (isset($output['@value'])) {
                            $output['@value'] .= ' ' . $text;
                        } else {
                            $output['@value'] = $text;
                        }
                    }
                } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                    $textOnly = false;
                    $key = $child->nodeName;
                    $value = $this->domNodeToArray($child);

                    if (isset($output[$key])) {
                        if (!is_array($output[$key]) || isset($output[$key][0]) === false) {
                            $output[$key] = [$output[$key]];
                        }
                        $output[$key][] = $value;
                    } else {
                        $output[$key] = $value;
                    }
                }
            }

            if ($textOnly && isset($output['@value']) && count($output) === 1) {
                return $output['@value'];
            }
        }

        return $output;
    }

    /**
     * @param DOMXPath $xpath
     * @param string $path
     * @param DOMNode|null $context
     * @return string|null
     */
    protected function getValue(DOMXPath $xpath, string $path, ?DOMNode $context = null): ?string
    {
        $value = trim($xpath->evaluate("string($path)", $context));
        return $value !== '' ? $value : null;
    }

    /**
     * @return array
     */
    abstract public function parse(): array;
}
