<?php

/**
 * The base session class used by the Services_Yadis_Manager.  This
 * class wraps the default PHP session machinery and should be
 * subclassed if your application doesn't use PHP sessioning.
 *
 * @package Yadis
 */
class Services_Yadis_PHPSession
{
    /**
     * Set a session key/value pair.
     *
     * @param string $name The name of the session key to add.
     * @param string $value The value to add to the session.
     */
    function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Get a key's value from the session.
     *
     * @param string $name The name of the key to retrieve.
     * @param string $default The optional value to return if the key
     * is not found in the session.
     * @return string $result The key's value in the session or
     * $default if it isn't found.
     */
    function get($name, $default=null)
    {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        } else {
            return $default;
        }
    }

    /**
     * Remove a key/value pair from the session.
     *
     * @param string $name The name of the key to remove.
     */
    function del($name)
    {
        unset($_SESSION[$name]);
    }
}
