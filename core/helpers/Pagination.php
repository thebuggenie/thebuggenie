<?php

namespace thebuggenie\core\helpers;

/**
 * Helper class that deals with pagination.
 *
 * @author Branko Majic <branko@majic.rs>
 * @version 4.2
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage main
 */


/**
 * Helper class that deals with pagination. Provides the following features:
 *
 * - Processes request to extract pagination arguments.
 * - Calculates what items should be part of the current (requested) page.
 * - Calculates pagination URLs.
 * - Supports multiple page sizes.
 * - Comes with special template for rendering the pagination elements in
 *   consistent manner.
 *
 * To use it, first you would add something similar to the following in your
 * controller code:
 *
 * // Parameters you need for initialising Pagination instance
 * // ($extra_get_parameters are optional).
 * $request = framework\Context::getRequest();
 * $my_items = your_function_that_grabs_items_in_an_array();
 * $base_url = "/someurl";
 * $extra_get_parameters = ['something1' => 'somevalue1', 'something2' => 'somevalue2'];
 *
 * $pagination = new Pagination($my_items, $base_url, $request, $extra_get_parameters);
 *
 * // Set-up the template context.
 * $this->my_items = $pagination->getPageItems();
 * $this->pagination = $pagination;
 *
 * With that out of the way, you can do something like this in the template:
 *
 * <?php foreach ($my_items as $my_item): ?>
 *   do something with $my_item
 * <?php endforeach ?>
 * <?php include_component('main/pagination', ['pagination' => $pagination]); ?>
 *
 */
class Pagination
{
    /**
     * Default page to show in case the request did not specify a page.
     *
     */
    const DEFAULT_PAGE = 1;

    /**
     * Default page size to use when displaying results.
     *
     */
    const DEFAULT_PAGE_SIZE = 20;

    /**
     * Available page sizes.
     *
     */
    const AVAILABLE_PAGE_SIZES = [20, 50, 100, 250, 500];

    /**
     * Maximum number of page URLs to display in pagination. The limit excludes
     * special links for first, previous, next, and last page.
     *
     */
    const PAGE_URL_LIMIT = 10;


    /**
     * Array containing all elements that should be included in pagination
     * calculation. Passed-in by the user.
     *
     */
    protected $_items;

    /**
     * Base URL passed-in by the user for calculating pagination URLs.
     *
     */
    protected $_base_url;

    /**
     * Requested page.
     *
     */
    protected $_page;

    /**
     * Requested page size.
     *
     */
    protected $_page_size;

    /**
     * Additional GET parameters to include in the URL generation.
     *
     */
    protected $_extra_get_parameters;

    /**
     * Prefix to use on top of base URL when generating navigation URLs. Used as
     * convenience property to avoid complicated logic for avoding duplicate '?'
     * and '&' characters in generated URLs.
     *
     */
    protected $_base_url_extra_parameter_prefix;

    /**
     * Initialises instance, storing passed-in parameters, and ensuring
     * passed-in values via requests object are valid.
     *
     * @param array $items
     *   Array with all the elements that should be taken into consideration for
     *   pagination.
     *
     * @param string $base_url
     *   Base URL for generating the pagination URLs. This should be a plain
     *   link - any GET parameters should be passed-in as part of
     *   $extra_get_parameters.
     *
     * @param \thebuggenie\core\framework\Request $request
     *   Original request object received by the code. The request object is
     *   used to extract information about the page number and page size. The
     *   following parameters are extracted from it: 'page' and 'page_size'.
     *
     * @param array $extra_get_parameters
     *   Additional set of GET parameters to include in generated navigation
     *   URLs. This should be a hash where both keys and values are
     *   strings. Keys 'page' and 'page_size' will be stripped from the array,
     *   while keys with null value will be ignored.
     *
     */
    function __construct($items, $base_url, $request, $extra_get_parameters = [])
    {
        // Store parameters passed-in by the user.
        $this->_items = $items;
        $this->_base_url = $base_url;
        $this->_extra_get_parameters = $extra_get_parameters;

        // Process user-supplied GET parameters to get them in key=value format.
        $get_parameters = [];
        foreach ($this->_extra_get_parameters as $parameter => $value)
        {
            if ($value !== null && $parameter != 'page' && $parameter != 'page_size')
            {
                $get_parameters[] = "${parameter}=${value}";
            }
        }

        // Determine the extra parameter prefix (this gets appended to base_url
        // before pagination GET parameters).
        if (count($get_parameters) > 0)
        {
            $this->_base_url_extra_parameter_prefix = "?" . implode("&", $get_parameters) . "&";
        }
        else
        {
            $this->_base_url_extra_parameter_prefix = "?";
        }

        // Extract the page number and page size from request.
        $this->_page = intval($request->getParameter('page'));
        $this->_page_size = intval($request->getParameter('page_size'));

        // Ensure page and page size are valid non-negative integer values.
        if ($this->_page < 1)
        {
            $this->_page = self::DEFAULT_PAGE;
        }

        if ($this->_page_size < 1)
        {
            $this->_page_size = self::DEFAULT_PAGE_SIZE;
        }

        // Ensure we are not out of bounds with page number.
        if ($this->_page > $this->getTotalPages())
        {
            $this->_page = $this->getTotalPages();
        }
    }

    /**
     * Returns total number of pages based on page size and number of elements.
     *
     *
     * @return int
     */
    public function getTotalPages()
    {
        return ceil(count($this->_items) / $this->_page_size);
    }

    /**
     * Returns an array containing all items that belong to requested page.
     *
     * @return array
     */
    public function getPageItems()
    {
        return array_slice($this->_items,
                           ($this->_page - 1) * $this->_page_size,
                           ($this->_page - 1) * $this->_page_size + $this->_page_size);
    }

    /**
     * Calculates page URLs.
     *
     *
     * @retval array
     *   Page URLs. Each URL is represented by an array of its own, with the
     *   following keys:
     *
     *   text
     *     Text to display to user for the page. This will be either a number,
     *     or a special character for denoting link to first ('&larrb;'),
     *     previous ('&laquo;'), next ('&raquo;'), or last ('&rarrb;') page.
     *
     *   hint
     *     Hint shown on mouse-over. If not one of the links for first,
     *     previous, next, or last pages, value will be null.
     *
     *   url
     *     URL to reach the page. If page number is equal to requested page,
     *     value will be null.
     */
    public function getPageURLs()
    {
        // Calculate the left and right boundary first, using the requested page
        // number as approximate middle.
        $left = $this->_page - floor((self::PAGE_URL_LIMIT-1)/2);
        $right = $this->_page + ceil((self::PAGE_URL_LIMIT-1)/2);

        // If either left or right boundary exceeds total number of pages, shift
        // the other one by the exceeded number.
        if ($left < 1)
        {
            $right = $right + (1 - $left);
        }

        if ($right > $this->getTotalPages())
        {
            $left = $left - ($right - $this->getTotalPages());
        }

        // Once shifting is done, make sure the left and right boundaries are
        // not invalid.
        if ($left < 1)
        {
            $left = 1;
        }

        if ($right > $this->getTotalPages())
        {
            $right = $this->getTotalPages();
        }

        // Array for storing all pagination URLs.
        $pagination_urls = [];

        // Pagination base URL.
        $pagination_url_base = $this->_base_url . $this->_base_url_extra_parameter_prefix . ($this->_page_size != self::DEFAULT_PAGE_SIZE ? "page_size={$this->_page_size}&" : "");

        // Add the first/previous page URLs.
        if ($left != 1)
        {
            $pagination_urls[] = ['text' => '&larrb;',
                                  'hint' => 'First page',
                                  'url'  => "{$pagination_url_base}page=1"];

            $pagination_urls[] = ['text' => '&laquo;',
                                  'hint' => 'Previous page',
                                  'url'  => "{$pagination_url_base}page=" . ($this->_page-1)];
        }

        // Process all "numeric" links.
        for ($i = $left; $i <= $right; $i++)
        {
            $pagination_urls[] = ['text' => $i,
                                  'hint' => ($i == $this->_page ? "Current page" : null),
                                  'url'  => ($i == $this->_page ? null : "{$pagination_url_base}page={$i}")];
        }

        // Add the next/last page URLs.
        if ($right != $this->getTotalPages())
        {
            $pagination_urls[] = ['text' => '&raquo;',
                                  'hint' => 'Next page',
                                  'url'  => "{$pagination_url_base}page=" . ($this->_page+1)];

            $pagination_urls[] = ['text' => '&rarrb;',
                                  'hint' => 'Last page',
                                  'url'  => "{$pagination_url_base}page=" . $this->getTotalPages()];
        }

        return $pagination_urls;
    }

    /**
     * Calculates page size URLs.
     *
     *
     * @retval array
     *
     *   Page size URLs. Each URL is represented by an array of its own, with
     *   the following keys:
     *
     *   text
     *     Text to display to user for the page size.
     *
     *   hint
     *     Hint shown on mouse-over.
     *
     *   url
     *     URL to switch the page size. If page size is equal to requested page
     *     size, value will be null.
     */
    public function getPageSizeURLs()
    {
        // Store all page size URLs within this variable.
        $page_size_urls = [];

        // Pagination base URL.
        $pagination_url_base = $this->_base_url . $this->_base_url_extra_parameter_prefix;

        foreach (self::AVAILABLE_PAGE_SIZES as $page_size)
        {
            $page_size_urls[] = ['text' => $page_size,
                                 'hint' => "Page size",
                                 'url'  => ($page_size == $this->_page_size ? null : "{$pagination_url_base}page_size={$page_size}")];
        }

        return $page_size_urls;
    }
}
