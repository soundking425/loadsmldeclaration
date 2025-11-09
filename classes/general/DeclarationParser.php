<?php

class DeclarationParser extends AbstractXmlParser
{
    private array $freeDoc;

    /**
     * @param string $filePath
     * @param string $freeDocFilePath
     * @throws Exception
     */
    public function __construct(string $filePath, string $freeDocFilePath)
    {
        parent::__construct($filePath);
        $this->freeDoc = (new FreeDocParser($freeDocFilePath))->parse();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function parse(): array
    {
        $xpathDT = $this->getDomXml($this->filePath);

        $dateDeal = $this->getValue($xpathDT, '//*[local-name()="GTDoutCustomsMark"]/*[local-name()="GTDID"]/*[local-name()="RegistrationDate"]');
        $declarationNumber = [
            $this->getValue($xpathDT, '//*[local-name()="GTDoutCustomsMark"]/*[local-name()="GTDID"]/*[local-name()="CustomsCode"]'),
            date('dmy', strtotime($dateDeal)),
            $this->getValue($xpathDT, '//*[local-name()="GTDoutCustomsMark"]/*[local-name()="GTDID"]/*[local-name()="GTDNumber"]'),
        ];

        $EsaCUGoods = $xpathDT->query('//*[local-name()="ESADout_CUGoods"]');
        $output = [];
        foreach ($EsaCUGoods as $CUGoodsKey => $CUGoods) {
            $output[$CUGoodsKey] = [
                'DATE_DEAL' => $dateDeal,
                'DECLARATION_NUMBER' => implode('/', $declarationNumber),
            ];

            $output[$CUGoodsKey]['GOODS'] = $this->getGoods($xpathDT, $CUGoods);
            $output[$CUGoodsKey]['LICENSE'] = $this->getLicense($xpathDT, $CUGoods);
        }

        return $output;
    }

    /**
     * @param DOMXPath $xpath
     * @param DOMNode|null $context
     * @return array
     */
    private function getGoods(DOMXPath $xpath, ?DOMNode $context): array
    {
        $result = [];
        $tnved = $this->getValue($xpath, './/*[local-name()="GoodsTNVEDCode"]', $context);
        $descNodes = $xpath->query('./*[local-name()="GoodsDescription"]', $context);
        $descList = [];
        foreach ($descNodes as $desc) {
            $text = trim($desc->nodeValue);
            if ($text !== '') {
                $descList[] = $text;
            }
        }
        $goodsGroupName = implode(', ', $descList);
        $goodsGroup = $xpath->query('./*[local-name()="GoodsGroupDescription"]', $context);

        foreach ($goodsGroup as $goods) {
            $goodsName = $this->getValue($xpath, './*[local-name()="GoodsDescription"]', $goods);
            if (empty($goodsName)) {
                $goodsName = $goodsGroupName;
            }

            $result[] = [
                'GOODS_NAME' => $goodsName,
                'TNVED' => $tnved,
                'PERMIT_NUMBER' => $this->freeDoc['PERMIT_NUMBER'],
                'PERMIT_DATE' => $this->freeDoc['PERMIT_DATE'],
                'CONTRACT' => $this->freeDoc['CONTRACT'],
                'RECIPIENT' => $this->freeDoc['RECIPIENT'],
                'PERMIT_RESULT' => $this->freeDoc['PERMIT_RESULT'],
                'OPERATION_INFO' => $this->freeDoc['OPERATION_INFO'],
                'GOODS_FUNC_DETAIL' => $this->freeDoc['GOODS_FUNC_DETAIL'],
            ];
        }

        return $result;
    }

    /**
     * @param DOMXPath $xpath
     * @param DOMNode|null $context
     * @return array
     */
    private function getLicense(DOMXPath $xpath, ?DOMNode $context): array
    {
        $res = [];
        $documentsGroup = $xpath->query('./*[local-name()="ESADout_CUPresentedDocument"]', $context);
        foreach ($documentsGroup as $doc) {
            $code = $this->getValue($xpath, './/*[local-name()="PresentedDocumentModeCode"]', $doc);
            if ($code === null || !str_starts_with($code, '01')) {
                continue;
            }

            $res[] = [
                'DOCUMENT_NUMBER' => $this->getValue($xpath, './/*[local-name()="PrDocumentNumber"]', $doc),
                'DOCUMENT_DATE' => $this->getValue($xpath, './/*[local-name()="PrDocumentDate"]', $doc),
                'DOCUMENT_NAME' => $this->getValue($xpath, './/*[local-name()="PrDocumentName"]', $doc),
                'DOCUMENT_MODE_CODE' => $code,
            ];
        }

        return $res;
    }

}


