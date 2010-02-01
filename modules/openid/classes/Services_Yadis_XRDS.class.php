<?php

/**
 * This class performs parsing of XRDS documents.
 *
 * You should not instantiate this class directly; rather, call
 * parseXRDS statically:
 *
 * <pre>  $xrds = Services_Yadis_XRDS::parseXRDS($xml_string);</pre>
 *
 * If the XRDS can be parsed and is valid, an instance of
 * Services_Yadis_XRDS will be returned.  Otherwise, null will be
 * returned.  This class is used by the Services_Yadis_Yadis::discover
 * method.
 *
 * @package Yadis
 */
class Services_Yadis_XRDS
{

    /**
     * Instantiate a Services_Yadis_XRDS object.  Requires an XPath
     * instance which has been used to parse a valid XRDS document.
     */
    function __construct($xmlParser, &$xrdNode)
    {
        $this->parser = $xmlParser;
        $this->xrdNode = $xrdNode;
        $this->serviceList = array();
        $this->_parse();
    }

    /**
     * Parse an XML string (XRDS document) and return either a
     * Services_Yadis_XRDS object or null, depending on whether the
     * XRDS XML is valid.
     *
     * @param string $xml_string An XRDS XML string.
     * @return mixed $xrds An instance of Services_Yadis_XRDS or null,
     * depending on the validity of $xml_string
     */
    public static function parseXRDS($xml_string, $extra_ns_map = null)
    {
        if (!$xml_string)
		{
            return null;
        }

        $parser = openidActions::Services_Yadis_getXMLParser();
        $ns_map = openidActions::getYadisNSmap();

        if ($extra_ns_map && is_array($extra_ns_map))
		{
            $ns_map = array_merge($ns_map, $extra_ns_map);
        }

        if (!($parser && $parser->init($xml_string, $ns_map)))
		{
            return null;
        }

        // Try to get root element.
        $root = $parser->evalXPath('/xrds:XRDS[1]');
        if (!$root)
		{
            return null;
        }

        if (is_array($root))
		{
            $root = $root[0];
        }

        $attrs = $parser->attributes($root);

        if (array_key_exists('xmlns:xrd', $attrs) && $attrs['xmlns:xrd'] != 'xri://$xrd*($v*2.0)')
		{
            return null;
        } 
		elseif (array_key_exists('xmlns', $attrs) && preg_match('/xri/', $attrs['xmlns']) && $attrs['xmlns'] != 'xri://$xrd*($v*2.0)')
		{
            return null;
        }

        // Get the last XRD node.
        $xrd_nodes = $parser->evalXPath('/xrds:XRDS[1]/xrd:XRD[last()]');

        if (!$xrd_nodes)
		{
            return null;
        }

        $xrds = new Services_Yadis_XRDS($parser, $xrd_nodes[0]);
        return $xrds;
    }

    /**
     * @access private
     */
    function _addService($priority, $service)
    {
        $priority = intval($priority);

        if (!array_key_exists($priority, $this->serviceList)) {
            $this->serviceList[$priority] = array();
        }

        $this->serviceList[$priority][] = $service;
    }

    /**
     * Creates the service list using nodes from the XRDS XML
     * document.
     *
     * @access private
     */
    function _parse()
    {
        $this->serviceList = array();

        $services = $this->parser->evalXPath('xrd:Service', $this->xrdNode);

        foreach ($services as $node)
		{
            $s = new Services_Yadis_Service();
            $s->element = $node;
            $s->parser = $this->parser;

            $priority = $s->getPriority();

            if ($priority === null)
			{
                $priority = openidActions::Services_Yadis_Max_Priority();
            }

            $this->_addService($priority, $s);
        }
    }

    /**
     * Returns a list of service objects which correspond to <Service>
     * elements in the XRDS XML document for this object.
     *
     * Optionally, an array of filter callbacks may be given to limit
     * the list of returned service objects.  Furthermore, the default
     * mode is to return all service objects which match ANY of the
     * specified filters, but $filter_mode may be
     * openidActions::SERVICES_YADIS_MATCH_ALL if you want to be sure that the
     * returned services match all the given filters.  See {@link
     * Services_Yadis_Yadis} for detailed usage information on filter
     * functions.
     *
     * @param mixed $filters An array of callbacks to filter the
     * returned services, or null if all services are to be returned.
     * @param integer $filter_mode openidActions::SERVICES_YADIS_MATCH_ALL or
     * openidActions::SERVICES_YADIS_MATCH_ANY, depending on whether the returned
     * services should match ALL or ANY of the specified filters,
     * respectively.
     * @return mixed $services An array of {@link
     * Services_Yadis_Service} objects if $filter_mode is a valid
     * mode; null if $filter_mode is an invalid mode (i.e., not
     * openidActions::SERVICES_YADIS_MATCH_ANY or openidActions::SERVICES_YADIS_MATCH_ALL).
     */
    function services($filters = null,
                      $filter_mode = openidActions::SERVICES_YADIS_MATCH_ANY)
    {

        $pri_keys = array_keys($this->serviceList);
        sort($pri_keys, SORT_NUMERIC);

        // If no filters are specified, return the entire service
        // list, ordered by priority.
        if (!$filters ||
            (!is_array($filters))) {

            $result = array();
            foreach ($pri_keys as $pri) {
                $result = array_merge($result, $this->serviceList[$pri]);
            }

            return $result;
        }

        // If a bad filter mode is specified, return null.
        if (!in_array($filter_mode, array(openidActions::SERVICES_YADIS_MATCH_ANY,
                                          openidActions::SERVICES_YADIS_MATCH_ALL))) {
            return null;
        }

        // Otherwise, use the callbacks in the filter list to
        // determine which services are returned.
        $filtered = array();

        foreach ($pri_keys as $priority_value) {
            $service_obj_list = $this->serviceList[$priority_value];

            foreach ($service_obj_list as $service) {

                $matches = 0;

                foreach ($filters as $filter) {
                    if (call_user_func_array($filter, array($service))) {
                        $matches++;

                        if ($filter_mode == openidActions::SERVICES_YADIS_MATCH_ANY) {
                            $pri = $service->getPriority();
                            if ($pri === null) {
                                $pri = openidActions::Services_Yadis_Max_Priority();
                            }

                            if (!array_key_exists($pri, $filtered)) {
                                $filtered[$pri] = array();
                            }

                            $filtered[$pri][] = $service;
                            break;
                        }
                    }
                }

                if (($filter_mode == openidActions::SERVICES_YADIS_MATCH_ALL) &&
                    ($matches == count($filters))) {

                    $pri = $service->getPriority();
                    if ($pri === null) {
                        $pri = openidActions::Services_Yadis_Max_Priority();
                    }

                    if (!array_key_exists($pri, $filtered)) {
                        $filtered[$pri] = array();
                    }
                    $filtered[$pri][] = $service;
                }
            }
        }

        $pri_keys = array_keys($filtered);
        sort($pri_keys, SORT_NUMERIC);

        $result = array();
        foreach ($pri_keys as $pri) {
            $result = array_merge($result, $filtered[$pri]);
        }

        return $result;
    }
}
