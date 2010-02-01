<?php

/**
 * This concrete implementation of Services_Yadis_XMLParser implements
 * the appropriate API for the 'domxml' extension which is typically
 * packaged with PHP 4.  This class will be used whenever the 'domxml'
 * extension is detected.  See the Services_Yadis_XMLParser class for
 * details on this class's methods.
 *
 * @package Yadis
 */
class Services_Yadis_domxml extends Services_Yadis_XMLParser
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
        $this->doc = @domxml_open_mem($xml_string, DOMXML_LOAD_PARSING,
                                      $this->errors);

        if (!$this->doc) {
            return false;
        }

        $this->xpath = $this->doc->xpath_new_context();

        return true;
    }

    function registerNamespace($prefix, $uri)
    {
        return xpath_register_ns($this->xpath, $prefix, $uri);
    }

    function &evalXPath($xpath, $node = null)
    {
        if ($node) {
            $result = @$this->xpath->xpath_eval($xpath, $node);
        } else {
            $result = @$this->xpath->xpath_eval($xpath);
        }

        if (!$result->nodeset) {
            $n = array();
            return $n;
        }

        return $result->nodeset;
    }

    function content($node)
    {
        if ($node) {
            return $node->get_content();
        }
    }

    function attributes($node)
    {
        if ($node) {
            $arr = $node->attributes();
            $result = array();

            if ($arr) {
                foreach ($arr as $attrnode) {
                    $result[$attrnode->name] = $attrnode->value;
                }
            }

            return $result;
        }
    }
}
