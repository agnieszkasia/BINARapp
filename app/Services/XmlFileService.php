<?php

namespace App\Services;

class XmlFileService
{
    public function convertDataToArray($xml)
    {
        $lineCount = count($xml->children());

        $data = array();
        for ($i = 0; $i < $lineCount; $i++) {
            $data[$i] = $xml->enumeration[$i]['value']." ".$xml->enumeration[$i]->documentation;
        }
        return $data;
    }
}
