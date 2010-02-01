<?php

/**
 * This concrete implementation of Services_Yadis_XMLParser implements
 * the appropriate API for the 'dom' extension which is typically
 * packaged with PHP 5.  This class will be used whenever the 'dom'
 * extension is detected.  See the Services_Yadis_XMLParser class for
 * details on this class's methods.
 *
 * @package Yadis
 */
class Services_Yadis_dom extends Services_Yadis_XMLParser
{
    function __construct()
    {
        $this->xml = null;
        $this->doc = null;
        $this->xpath = null;
        $this->errors = array();
    }

    function setXML($xml_string)
    {
        $this->xml = $xml_string;
        $this->doc = new DOMDocument();

        if (!$this->doc) {
            return false;
        }

        if (!@$this->doc->loadXML($xml_string)) {
            return false;
        }

        $this->xpath = new DOMXPath($this->doc);

        if ($this->xpath) {
            return true;
        } else {
            return false;
        }
    }

    function registerNamespace($prefix, $uri)
    {
        return $this->xpath->registerNamespace($prefix, $uri);
    }

    function &evalXPath($xpath, $node = null)
    {
        if ($node) {
            $result = @$this->xpath->evaluate($xpath, $node);
        } else {
            $result = @$this->xpath->evaluate($xpath);
        }

        $n = array();

		if ($result)
		{
			for ($i = 0; $i < $result->length; $i++) {
				$n[] = $result->item($i);
			}
		}

        return $n;
    }

    function content($node)
    {
        if ($node) {
            return $node->textContent;
        }
    }

    function attributes($node)
    {
        if ($node) {
            $arr = $node->attributes;
            $result = array();

            if ($arr) {
                for ($i = 0; $i < $arr->length; $i++) {
                    $node = $arr->item($i);
                    $result[$node->nodeName] = $node->nodeValue;
                }
            }

            return $result;
        }
    }
}