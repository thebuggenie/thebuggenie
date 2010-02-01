<?php

/**
 * This module contains the CURL-based HTTP fetcher implementation.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: See the COPYING file included in this distribution.
 *
 * @package Yadis
 * @author JanRain, Inc. <openid@janrain.com>
 * @copyright 2005 Janrain, Inc.
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */

/**
 * A paranoid {@link Services_Yadis_HTTPFetcher} class which uses CURL
 * for fetching.
 *
 * @package Yadis
 */
class Services_Yadis_ParanoidHTTPFetcher extends Services_Yadis_HTTPFetcher
{
    function Services_Yadis_ParanoidHTTPFetcher($timeout)
    {
        if (!function_exists('curl_init')) {
            trigger_error("Cannot use this class; CURL extension not found",
                          E_USER_ERROR);
        }

        $this->timeout = $timeout;
        $this->headers = array();
        $this->data = "";

        $this->reset();
    }

    function reset()
    {
        $this->headers = array();
        $this->data = "";
    }

    /**
     * @access private
     */
    function _writeHeader($ch, $header)
    {
        array_push($this->headers, rtrim($header));
        return strlen($header);
    }

    /**
     * @access private
     */
    function _writeData($ch, $data)
    {
        $this->data .= $data;
        return strlen($data);
    }

    /**
     * Fetches the specified URL using optional extra headers and
     * returns the server's response.  Uses the CURL extension.
     *
     * @param string $url The URL to be fetched.
     * @param array $extra_headers An array of header strings
     * (e.g. "Accept: text/html").
     * @return mixed $result An array of ($code, $url, $headers,
     * $body) if the URL could be fetched; null if the URL does not
     * pass the URLHasAllowedScheme check or if the server's response
     * is malformed.
     */
    function get($url, $extra_headers = null)
    {
        $stop = time() + $this->timeout;
        $off = $this->timeout;

        $redir = true;

        while ($redir && ($off > 0)) {
            $this->reset();

            $c = curl_init();
            curl_setopt($c, CURLOPT_NOSIGNAL, true);

            if (!$this->allowedURL($url)) {
                trigger_error(sprintf("Fetching URL not allowed: %s", $url),
                              E_USER_WARNING);
                return null;
            }

            curl_setopt($c, CURLOPT_WRITEFUNCTION,
                        array(&$this, "_writeData"));
            curl_setopt($c, CURLOPT_HEADERFUNCTION,
                        array(&$this, "_writeHeader"));

            if ($extra_headers) {
                curl_setopt($c, CURLOPT_HTTPHEADER, $extra_headers);
            }

            curl_setopt($c, CURLOPT_TIMEOUT, $off);
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);

            curl_exec($c);

            $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
            $body = $this->data;
            $headers = $this->headers;

            if (!$code)
			{
                trigger_error("No HTTP code returned", E_USER_WARNING);
                return null;
            }

            if (in_array($code, array(301, 302, 303, 307)))
			{
                $url = $this->_findRedirect($headers);
                $redir = true;
            } 
			else
			{
                $redir = false;
                curl_close($c);

                $new_headers = array();
                foreach ($headers as $header)
				{
					if (strpos($header, ':') !== false)
					{
						list($name, $value) = explode(": ", $header, 2);
						$new_headers[$name] = $value;
					}
                }

                return new Services_Yadis_HTTPResponse($url, $code, $new_headers, $body);
            }

            $off = $stop - time();
        }

        trigger_error(sprintf("Timed out fetching: %s", $url), E_USER_WARNING);

        return null;
    }
}