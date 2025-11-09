<?php

class FreeDocParser extends AbstractXmlParser
{
    /**
     * @return array
     * @throws Exception
     */
    public function parse(): array
    {
        $xpath = $this->getDomXml($this->filePath);
        return [
            'PERMIT_NUMBER' => $this->getValue($xpath, '/*[local-name()="FreeDoc"]/*[local-name()="DocumentHead"]/*[local-name()="DocumentNumber"]'),
            'PERMIT_DATE' => $this->getValue($xpath, '/*[local-name()="FreeDoc"]/*[local-name()="DocumentHead"]/*[local-name()="DocumentDate"]'),
            'CONTRACT' => $this->getValue($xpath, '/*[local-name()="FreeDoc"]/*[local-name()="DocumentBody"]/*[local-name()="TextSection"][2]/*[local-name()="TextPara"]'),
            'RECIPIENT' => $this->getValue($xpath, '/*[local-name()="FreeDoc"]/*[local-name()="DocumentBody"]/*[local-name()="TextSection"][5]/*[local-name()="TextPara"]'),
            'PERMIT_RESULT' => $this->getValue($xpath, '/*[local-name()="FreeDoc"]/*[local-name()="DocumentBody"]/*[local-name()="TextSection"][8]/*[local-name()="TextPara"]'),
            'OPERATION_INFO' => $this->getValue($xpath, '/*[local-name()="FreeDoc"]/*[local-name()="DocumentBody"]/*[local-name()="TextSection"][3]/*[local-name()="TextPara"]'),
            'GOODS_FUNC_DETAIL' => '',
        ];
    }
}
