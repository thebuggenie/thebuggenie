<?php

/**
 * This class represents a <Service> element in an XRDS document.
 * Objects of this type are returned by
 * Services_Yadis_XRDS::services() and
 * Services_Yadis_Yadis::services().  Each object corresponds directly
 * to a <Service> element in the XRDS and supplies a
 * getElements($name) method which you should use to inspect the
 * element's contents.  See {@link Services_Yadis_Yadis} for more
 * information on the role this class plays in Yadis discovery.
 *
 * @package Yadis
 */
class Services_Yadis_Service
{

    /**
     * Creates an empty service object.
     */
    function Services_Yadis_Service()
    {
        $this->element = null;
        $this->parser = null;
    }

    /**
     * Return the URIs in the "Type" elements, if any, of this Service
     * element.
     *
     * @return array $type_uris An array of Type URI strings.
     */
    function getTypes()
    {
        $t = array();
        foreach ($this->getElements('xrd:Type') as $elem) {
            $c = $this->parser->content($elem);
            if ($c) {
                $t[] = $c;
            }
        }
        return $t;
    }

    /**
     * Return the URIs in the "URI" elements, if any, of this Service
     * element.  The URIs are returned sorted in priority order.
     *
     * @return array $uris An array of URI strings.
     */
    function getURIs()
    {
        $uris = array();
        $last = array();

        foreach ($this->getElements('xrd:URI') as $elem) {
            $uri_string = $this->parser->content($elem);
            $attrs = $this->parser->attributes($elem);
            if ($attrs &&
                array_key_exists('priority', $attrs)) {
                $priority = intval($attrs['priority']);
                if (!array_key_exists($priority, $uris)) {
                    $uris[$priority] = array();
                }

                $uris[$priority][] = $uri_string;
            } else {
                $last[] = $uri_string;
            }
        }

        $keys = array_keys($uris);
        sort($keys);

        // Rebuild array of URIs.
        $result = array();
        foreach ($keys as $k) {
            $new_uris = openidActions::Services_Yadis_array_scramble($uris[$k]);
            $result = array_merge($result, $new_uris);
        }

        $result = array_merge($result,
                              openidActions::Services_Yadis_array_scramble($last));

        return $result;
    }

    /**
     * Returns the "priority" attribute value of this <Service>
     * element, if the attribute is present.  Returns null if not.
     *
     * @return mixed $result Null or integer, depending on whether
     * this Service element has a 'priority' attribute.
     */
    function getPriority()
    {
        $attributes = $this->parser->attributes($this->element);

        if (array_key_exists('priority', $attributes)) {
            return intval($attributes['priority']);
        }

        return null;
    }

    /**
     * Used to get XML elements from this object's <Service> element.
     *
     * This is what you should use to get all custom information out
     * of this element. This is used by service filter functions to
     * determine whether a service element contains specific tags,
     * etc.  NOTE: this only considers elements which are direct
     * children of the <Service> element for this object.
     *
     * @param string $name The name of the element to look for
     * @return array $list An array of elements with the specified
     * name which are direct children of the <Service> element.  The
     * nodes returned by this function can be passed to $this->parser
     * methods (see {@link Services_Yadis_XMLParser}).
     */
    function getElements($name)
    {
        return $this->parser->evalXPath($name, $this->element);
    }
}
