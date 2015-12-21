<?php

namespace XmlUtils;

use DOMDocument;
use SimpleXMLElement;

final class XmlUtils
{
    public static function getSingleValue($xmlObj, $xpathString)
    {
        $val = null;

        if ($xmlObj instanceof DOMDocument) {
            $xpath = new \DOMXpath($xmlObj);
            $nodes = $xpath->query($xpathString);
            if (1 === $nodes->length) {
                $val = $nodes->item(0)->nodeValue;
            }

            unset($xpath);
        } elseif ($xmlObj instanceof SimpleXMLElement) {
            $nodes = $xmlObj->xpath($xpathString);
            if (1 === count($nodes)) {
                $val = trim((string) $nodes[0]);
            }
        } else {
            throw new \InvalidArgumentException();
        }

        return $val;
    }
}
