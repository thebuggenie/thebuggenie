<?php

/**
 * The base class for wrappers for available PHP XML-parsing
 * extensions.  To work with this Yadis library, subclasses of this
 * class MUST implement the API as defined in the remarks for this
 * class.  Subclasses of Services_Yadis_XMLParser are used to wrap
 * particular PHP XML extensions such as 'domxml'.  These are used
 * internally by the library depending on the availability of
 * supported PHP XML extensions.
 *
 * @package Yadis
 */
class Services_Yadis_XMLParser
{
    /**
     * Initialize an instance of Services_Yadis_XMLParser with some
     * XML and namespaces.  This SHOULD NOT be overridden by
     * subclasses.
     *
     * @param string $xml_string A string of XML to be parsed.
     * @param array $namespace_map An array of ($ns_name => $ns_uri)
     * to be registered with the XML parser.  May be empty.
     * @return boolean $result True if the initialization and
     * namespace registration(s) succeeded; false otherwise.
     */
    function init($xml_string, $namespace_map)
    {
        if (!$this->setXML($xml_string)) {
            return false;
        }

        foreach ($namespace_map as $prefix => $uri) {
            if (!$this->registerNamespace($prefix, $uri)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Register a namespace with the XML parser.  This should be
     * overridden by subclasses.
     *
     * @param string $prefix The namespace prefix to appear in XML tag
     * names.
     *
     * @param string $uri The namespace URI to be used to identify the
     * namespace in the XML.
     *
     * @return boolean $result True if the registration succeeded;
     * false otherwise.
     */
    function registerNamespace($prefix, $uri)
    {
        // Not implemented.
    }

    /**
     * Set this parser object's XML payload.  This should be
     * overridden by subclasses.
     *
     * @param string $xml_string The XML string to pass to this
     * object's XML parser.
     *
     * @return boolean $result True if the initialization succeeded;
     * false otherwise.
     */
    function setXML($xml_string)
    {
        // Not implemented.
    }

    /**
     * Evaluate an XPath expression and return the resulting node
     * list.  This should be overridden by subclasses.
     *
     * @param string $xpath The XPath expression to be evaluated.
     *
     * @param mixed $node A node object resulting from a previous
     * evalXPath call.  This node, if specified, provides the context
     * for the evaluation of this xpath expression.
     *
     * @return array $node_list An array of matching opaque node
     * objects to be used with other methods of this parser class.
     */
    function &evalXPath($xpath, $node = null)
    {
        // Not implemented.
    }

    /**
     * Return the textual content of a specified node.
     *
     * @param mixed $node A node object from a previous call to
     * $this->evalXPath().
     *
     * @return string $content The content of this node.
     */
    function content($node)
    {
        // Not implemented.
    }

    /**
     * Return the attributes of a specified node.
     *
     * @param mixed $node A node object from a previous call to
     * $this->evalXPath().
     *
     * @return array $attrs An array mapping attribute names to
     * values.
     */
    function attributes($node)
    {
        // Not implemented.
    }
}
