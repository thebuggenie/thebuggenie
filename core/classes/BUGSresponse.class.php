<?php

	/**
	 * Response class used in the MVC part of the framework
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mvc
	 */

	/**
	 * Response class used in the MVC part of the framework
	 *
	 * @package thebuggenie
	 * @subpackage mvc
	 */
	class BUGSresponse
	{

		const DECORATE_NONE = 0;
		const DECORATE_HEADER = 1;
		const DECORATE_FOOTER = 2;
		const DECORATE_BOTH = 3;
		const DECORATE_CUSTOM = 4;

		/**
		 * The current page (used to identify the selected tab
		 * 
		 * @var string
		 */
		protected $_page = null;
		
		/**
		 * Current page title
		 * 
		 * @var string
		 */
		protected $_title = null;
		
		/**
		 * List of headers
		 * 
		 * @var array
		 */
		protected $_headers = array();
		
		/**
		 * List of javascripts
		 * 
		 * @var array
		 */
		protected $_javascripts = array();
		
		/**
		 * Current response status
		 * 
		 * @var integer
		 */
		protected $_http_status = 200;
		
		/**
		 * Response content-type
		 * 
		 * @var string
		 */
		protected $_content_type = 'text/html';
		
		/**
		 * Current template
		 * 
		 * @var string
		 */
		protected $_template = '';

		/**
		 * What decoration to use (default normal)
		 * 
		 * @var integer
		 */
		protected $_decoration = 3;
		
		/**
		 * Decoration used to decorate the header
		 * 
		 * @var string
		 */
		protected $_decor_header = null;
		
		/**
		 * Decoration used to decorate the footer
		 * @var unknown_type
		 */
		protected $_decor_footer = null;
		
		/**
		 * Set the template
		 * 
		 * @param string $template The template name
		 */
		public function setTemplate($template)
		{
			$this->_template = $template;
		}

		/**
		 * Return current template
		 * 
		 * @return string
		 */
		public function getTemplate()
		{
			return $this->_template;
		}
		
		/**
		 * Set which page we're on
		 * 
		 * @param string $page A unique page identifier
		 */
		public function setPage($page)
		{
			$this->_page = $page;
		}

		/**
		 * Set the current title
		 * 
		 * @param string $title The title
		 */
		public function setTitle($title)
		{
			$this->_title = $title;
		}

		/**
		 * Get the current title
		 * 
		 * @return string
		 */
		public function getTitle()
		{
			return $this->_title;
		}
		
		/**
		 * Check to see if a title is set
		 * 
		 * @return boolean
		 */
		public function hasTitle()
		{
			return (trim($this->_title) != '') ? true : false;
		}
		
		/**
		 * Get the current page name
		 * 
		 * @return string
		 */
		public function getPage()
		{
			return $this->_page;
		}
		
		/**
		 * Add a header
		 * 
		 * @param string $header The header to add
		 */
		public function addHeader($header)
		{
			$this->_headers[] = $header;
		}
		
		/**
		 * Add a javascript
		 * 
		 * @param string $javascript javascript name
		 */
		public function addJavascript($javascript)
		{
			$this->_javascripts[$javascript] = $javascript;
		}
		
		/**
		 * Forward the user to a different URL
		 * 
		 * @param string $url the url to forward to
		 * @param integer $code HTTP status code
		 */
		public function headerRedirect($url, $code)
		{
			BUGSlogging::log('Running header redirect function');
			$this->clearHeaders();
			$this->setHttpStatus($code);
			$this->addHeader("Location: $url");
			$this->renderHeaders();
			exit();
		}

		/**
		 * Forward the user to a different url via meta tag
		 * 
		 * @param string $url The url to forward to
		 */
		static function metaForward($url)
		{
			print "<meta http-equiv=\"refresh\" content=\"0;URL={$url}\">";
			exit();
		}
		
		/**
		 * Set the HTTP status code
		 * 
		 * @param integer $code The code to set
		 */
		public function setHttpStatus($code)
		{
			$this->_http_status = $code;
		}
		
		/**
		 * Whether we're decorating with the header or not
		 * 
		 * @return string
		 */
		public function doDecorateHeader()
		{
			return ($this->_decoration == 1 || $this->_decoration == 3) ? true : false;
		}
		
		/**
		 * Whether we're decorating with the footer or not
		 * @return unknown_type
		 */
		public function doDecorateFooter()
		{
			return ($this->_decoration == 2 || $this->_decoration == 3) ? true : false;
		}
		
		/**
		 * Set the decoration mode
		 * 
		 * @param integer $mode The mode used (see class constants)
		 * @param array $params [optional] array('header' => templatename, 'footer' => templatename) optional decoration specifiers
		 * 
		 * @return null
		 */
		public function setDecoration($mode, $params = null)
		{
			$this->_decoration = $mode;
			if (is_array($params))
			{
				if (array_key_exists('header', $params)) $this->_decor_header = $params['header'];
				if (array_key_exists('footer', $params)) $this->_decor_footer = $params['footer'];
			}
		}
		
		public function getHeaderDecoration()
		{
			return $this->_decor_header;
		}
		
		public function getFooterDecoration()
		{
			return $this->_decor_footer;
		}
		
		/**
		 * Sets a cookie on the client, default expiration is one day
		 *  
		 * @param $key string the cookie key
		 * @param $value string the cookie value
		 * @param $expiration integer when the cookie expires (seconds from now)
		 * 
		 * @return bool
		 */
		public function setCookie($key, $value, $expiration = 864000)
		{
			setcookie($key, $value, $_SERVER["REQUEST_TIME"] + $expiration);
			return true;
		}
		
		/**
		 * Deletes a cookie on the client
		 * 
		 * @param $key string the cookie key to delete
		 * 
		 * @return bool
		 */
		public function deleteCookie($key)
		{
			setcookie($key, '', $_SERVER["REQUEST_TIME"] - 36000);
			return true;
		}		

		/**
		 * Render current headers
		 */
		public function renderHeaders()
		{
			header("HTTP/1.0 ".$this->_http_status);
			/* headers to stop caching in browsers and proxies */
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header ("Pragma: no-cache"); // HTTP/1.0
			header ("Content-Type: " . $this->_content_type . "; charset=" . BUGScontext::getI18n()->getCharset());

			foreach ($this->_headers as $header)
			{
				header($header);
			}
		}
		
		/**
		 * Set the current response content type (default text/html)
		 * 
		 * @param string $content_type The content type to set
		 */
		public function setContentType($content_type)
		{
			$this->_content_type = $content_type;
		}
		
		/**
		 * Return the current response content type
		 * 
		 * @return string
		 */
		public function getContentType()
		{
			return $this->_content_type;
		}
		
		/**
		 * Return all active javascripts
		 * 
		 * @return array
		 */
		public function getJavascripts()
		{
			return $this->_javascripts;
		}

		/**
		 * Clear current headers
		 */
		public function clearHeaders()
		{
			$this->_headers = array();
			
		}

		
	}
