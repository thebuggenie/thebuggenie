<?php

/**
 * This module contains the plain non-curl HTTP fetcher
 * implementation.
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
 * This class implements a plain, hand-built socket-based fetcher
 * which will be used in the event that CURL is unavailable.
 *
 * @package Yadis
 */
class Services_Yadis_PlainHTTPFetcher extends Services_Yadis_HTTPFetcher
{
    function Services_Yadis_PlainHTTPFetcher($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Fetches the specified URL using optional extra headers and
     * returns the server's response.  Uses plain PHP library calls
     * and doesn't rely on any extensions.
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
        if (!$this->allowedURL($url)) {
            trigger_error("Bad URL scheme in url: " . $url,
                          E_USER_WARNING);
            return null;
        }

        $redir = true;

        $stop = time() + $this->timeout;
        $off = $this->timeout;

        while ($redir && ($off > 0)) {

            $parts = parse_url($url);

            $default_port = false;

            // Set a default port.
            if (!array_key_exists('port', $parts)) {
                $default_port = true;
                if ($parts['scheme'] == 'http') {
                    $parts['port'] = 80;
                } elseif ($parts['scheme'] == 'https') {
                    $parts['port'] = 443;
                } else {
                    trigger_error("fetcher post method doesn't support " .
                                  " scheme '" . $parts['scheme'] .
                                  "', no default port available",
                                  E_USER_WARNING);
                    return null;
                }
            }

            $host = $parts['host'];

            if ($parts['scheme'] == 'https') {
                $host = 'ssl://' . $host;
            }

            $user_agent = "PHP Yadis Library Fetcher";

            $headers = array(
                             "GET ".(array_key_exists('path', $parts) ?
                              $parts['path'] : "").
                             (array_key_exists('query', $parts) ?
                              "?".$parts['query'] : "").
                                  " HTTP/1.1",
                             "User-Agent: $user_agent",
                             "Host: ".$parts['host'].(!$default_port ?
                                                      ":".$parts['port'] : ""),
                             "Port: ".$parts['port'],
                             "Cache-Control: no-cache",
                             "Connection: close");

            if ($extra_headers) {
                foreach ($extra_headers as $h) {
                    $headers[] = $h;
                }
            }

            $errno = 0;
            $errstr = '';

			$sock = fsockopen($host, $parts['port'], $errno, $errstr, $this->timeout);
            if ($sock === false) {
                return false;
            }

            stream_set_timeout($sock, $this->timeout);

            fputs($sock, implode("\r\n", $headers) . "\r\n\r\n");

            $data = fgets($sock);
            while (!feof($sock)) {
                $chunk = fgets($sock, 1024);
                $data .= $chunk;
            }

            fclose($sock);

            // Split response into header and body sections
            list($headers, $body) = explode("\r\n\r\n", $data, 2);
            $headers = explode("\r\n", $headers);

            $http_code = explode(" ", $headers[0]);
            $code = $http_code[1];

			$body = substr($body, strpos($body, '<'));

            if (in_array($code, array('301', '302'))) {
                $url = $this->_findRedirect($headers);
                $redir = true;
            } else {
                $redir = false;
            }

            $off = $stop - time();
        }

        $new_headers = array();

        foreach ($headers as $header) {
            if (preg_match("/:/", $header)) {
                list($name, $value) = explode(": ", $header, 2);
                $new_headers[$name] = $value;
            }
        }

        return new Services_Yadis_HTTPResponse($url, $code,
                                               $new_headers, $body);
    }
}