<?php

/**
 * Yadis service manager to be used during yadis-driven authentication
 * attempts.
 *
 * @package Yadis
 */

/**
 * State management for discovery.
 *
 * High-level usage pattern is to call .getNextService(discover) in
 * order to find the next available service for this user for this
 * session. Once a request completes, call .finish() to clean up the
 * session state.
 *
 * @package Yadis
 */
class Services_Yadis_Discovery
{

    /**
     * @access private
     */
    var $DEFAULT_SUFFIX = 'auth';

    /**
     * @access private
     */
    var $PREFIX = '_yadis_services_';

    /**
     * Initialize a discovery object.
     *
     * @param Services_Yadis_PHPSession $session An object which
     * implements the Services_Yadis_PHPSession API.
     * @param string $url The URL on which to attempt discovery.
     * @param string $session_key_suffix The optional session key
     * suffix override.
     */
    function Services_Yadis_Discovery(&$session, $url,
                                      $session_key_suffix = null)
    {
        /// Initialize a discovery object
        $this->session = $session;
        $this->url = $url;
        if ($session_key_suffix === null) {
            $session_key_suffix = $this->DEFAULT_SUFFIX;
        }

        $this->session_key_suffix = $session_key_suffix;
        $this->session_key = $this->PREFIX . $this->session_key_suffix;
    }

    /**
     * Return the next authentication service for the pair of
     * user_input and session. This function handles fallback.
     */
    function getNextService($discover_cb, &$fetcher)
    {
        $manager = $this->getManager();
        if ((!$manager) ||
            $manager->stale) {
            $this->destroyManager();
            $http_response = array();

            $services = call_user_func($discover_cb, $this->url,
                                       $fetcher);

            $manager = $this->createManager($services, $this->url);
        }

        if ($manager) {
            $service = $manager->nextService();
            $this->session->set($this->session_key, serialize($manager));
        } else {
            $service = null;
        }

        return $service;
    }

    /**
     * Clean up Yadis-related services in the session and return the
     * most-recently-attempted service from the manager, if one
     * exists.
     */
    function cleanup()
    {
        $manager = $this->getManager();
        if ($manager) {
            $service = $manager->current();
            $this->destroyManager();
        } else {
            $service = null;
        }

        return $service;
    }

    /**
     * @access private
     */
    function getSessionKey()
    {
        // Get the session key for this starting URL and suffix
        return $this->PREFIX . $this->session_key_suffix;
    }

    /**
     * @access private
     */
    function getManager()
    {
        // Extract the YadisServiceManager for this object's URL and
        // suffix from the session.

        $manager_str = $this->session->get($this->getSessionKey());
        $manager = null;

        if ($manager_str !== null) {
            $manager = unserialize($manager_str);
        }

        if ($manager && $manager->forURL($this->url)) {
            return $manager;
        } else {
            return null;
        }
    }

    /**
     * @access private
     */
    function &createManager($services, $yadis_url = null)
    {
        $key = $this->getSessionKey();
        if ($this->getManager()) {
            return $this->getManager();
        }

        if (!$services) {
            return null;
        }

        $manager = new Services_Yadis_Manager($this->url, $yadis_url,
                                              $services, $key);
        $this->session->set($this->session_key, serialize($manager));
        return $manager;
    }

    /**
     * @access private
     */
    function destroyManager()
    {
        if ($this->getManager() !== null) {
            $key = $this->getSessionKey();
            $this->session->del($key);
        }
    }
}
