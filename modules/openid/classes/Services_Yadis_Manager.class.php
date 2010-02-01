<?php

/**
 * The Yadis service manager which stores state in a session and
 * iterates over <Service> elements in a Yadis XRDS document and lets
 * a caller attempt to use each one.  This is used by the Yadis
 * library internally.
 *
 * @package Yadis
 */
class Services_Yadis_Manager
{

    /**
     * Intialize a new yadis service manager.
     *
     * @access private
     */
    function Services_Yadis_Manager($starting_url, $yadis_url,
                                    $services, $session_key)
    {
        // The URL that was used to initiate the Yadis protocol
        $this->starting_url = $starting_url;

        // The URL after following redirects (the identifier)
        $this->yadis_url = $yadis_url;

        // List of service elements
        $this->services = $services;

        $this->session_key = $session_key;

        // Reference to the current service object
        $this->_current = null;

        // Stale flag for cleanup if PHP lib has trouble.
        $this->stale = false;
    }

    /**
     * @access private
     */
    function length()
    {
        // How many untried services remain?
        return count($this->services);
    }

    /**
     * Return the next service
     *
     * $this->current() will continue to return that service until the
     * next call to this method.
     */
    function nextService()
    {

        if ($this->services) {
            $this->_current = array_shift($this->services);
        } else {
            $this->_current = null;
        }

        return $this->_current;
    }

    /**
     * @access private
     */
    function current()
    {
        // Return the current service.
        // Returns None if there are no services left.
        return $this->_current;
    }

    /**
     * @access private
     */
    function forURL($url)
    {
        return in_array($url, array($this->starting_url, $this->yadis_url));
    }

    /**
     * @access private
     */
    function started()
    {
        // Has the first service been returned?
        return $this->_current !== null;
    }
}
