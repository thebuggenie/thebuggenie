<?php

	/**
	 * Response class used in the MVC part of the framework
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
	class TBGResponse
	{

		const DECORATE_NONE = 0;
		const DECORATE_HEADER = 1;
		const DECORATE_FOOTER = 2;
		const DECORATE_DEFAULT = 3;
		const DECORATE_CUSTOM = 4;

		/**
		 * The current page (used to identify the selected tab
		 * 
		 * @var string
		 */
		protected $_page = null;
		
		/**
		 * Breadcrumb trail for the current page
		 * 
		 * @var array
		 */
		protected $_breadcrumb = null;
		
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
		 * List of stylesheets
		 *
		 * @var array
		 */
		protected $_stylesheets = array();

		/**
		 * List of feeds
		 *
		 * @var array
		 */
		protected $_feeds = array();

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
		 * Whether to show the project menu strip or not
		 *
		 * @var boolean
		 */
		protected $_project_menu_strip_visible = true;

		public function ajaxResponseText($code, $error)
		{
			if (TBGContext::isDebugMode()) return true;

			$this->cleanBuffer();
			$this->setContentType('application/json');
			$this->setHttpStatus($code);
			$this->renderHeaders();
			echo json_encode(array('error' => $error));
			die();
		}

		public function setupResponseContentType($request_content_type)
		{
			$this->setDecoration(self::DECORATE_NONE);
			switch ($request_content_type)
			{
				case 'xml':
					$this->setContentType('application/xml');
					break;
				case 'rss':
					$this->setContentType('application/xml');
					break;
				case 'json':
					$this->setContentType('application/json');
					break;
				case 'csv':
					$this->setContentType('text/csv');
					break;
				default:
					$this->setDecoration(self::DECORATE_DEFAULT);
					break;
			}

		}

		public function cleanBuffer()
		{
			$ob_status = ob_get_status();
			if (!empty($ob_status) && $ob_status['status'] != PHP_OUTPUT_HANDLER_END)
			{
				ob_end_clean();
			}
		}

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
		 * Set the breadcrumb trail for the current page
		 * 
		 * @param array $breadcrumb 
		 */
		public function setBreadcrumb($breadcrumb)
		{
			$this->_breadcrumb = $breadcrumb;
		}

		/**
		 * Add to the breadcrumb trail for the current page
		 * 
		 * @param string $breadcrumb 
		 * @param string $url[optional]
		 */
		public function addBreadcrumb($breadcrumb, $url = null, $subitems = null, $class = null)
		{
			if ($this->_breadcrumb === null)
			{
				$this->_breadcrumb = array();
				TBGContext::populateBreadcrumbs();
			}

			$this->_breadcrumb[] = array('title' => $breadcrumb, 'url' => $url, 'subitems' => $subitems, 'class' => $class);
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
		 * Return the breadcrumb trail for the current page
		 * 
		 * @return array
		 */
		public function getBreadcrumbs()
		{
			if (!is_array($this->_breadcrumb))
			{
				$this->_breadcrumb = array();
				TBGContext::populateBreadcrumbs();
			}
			return $this->_breadcrumb;
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
		 * @param bool $minify Run through minify/content server
		 * @param bool $important Mark this script for being loaded before others
		 */
		public function addJavascript($javascript, $minify = true, $important = false)
		{
			if ($important)
			{
				$temp = array();
				$temp[$javascript] = $minify;
				$this->_javascripts = array_merge($temp, $this->_javascripts);
			}
			else
			{
				$this->_javascripts[$javascript] = $minify;
			}
		}
		
		/**
		 * Add a stylesheet
		 *
		 * @param string $stylesheet stylesheet name
		 * @param bool $minify Run through minify/content server
		 * @param bool $important Mark this stylesheet for being loaded before others
		 */
		public function addStylesheet($stylesheet, $minify = true, $important = false)
		{
			if ($important)
			{
				$temp = array();
				$temp[$stylesheet] = $minify;
				$this->_stylesheets = array_merge($temp, $this->_stylesheets);
			}
			else
			{
				$this->_stylesheets[$stylesheet] = $minify;
			}
		}

		/**
		 * Add a feed
		 *
		 * @param string $url feed url
		 * @param string $description feed description
		 */
		public function addFeed($url, $description)
		{
			$this->_feeds[$url] = $description;
		}

		/**
		 * Forward the user to a different URL
		 * 
		 * @param string $url the url to forward to
		 * @param integer $code HTTP status code
		 */
		public function headerRedirect($url, $code = 302)
		{
			TBGLogging::log('Running header redirect function');
			$this->clearHeaders();
			$this->setHttpStatus($code);
			if (TBGContext::getRequest()->isAjaxCall() || TBGContext::getRequest()->getRequestedFormat() == 'json')
			{
				$this->renderHeaders();
			}
			else
			{
				$this->addHeader("Location: $url");
				$this->renderHeaders();
			}
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
		 * Get the HTTP status code
		 */
		public function getHttpStatus()
		{
			return $this->_http_status;
		}

		/**
		 * Whether we're decorating with the header or not
		 * 
		 * @return string
		 */
		public function doDecorateHeader()
		{
			return ($this->_decoration == self::DECORATE_HEADER || ($this->_decoration == self::DECORATE_CUSTOM && $this->_decor_header)) ? true : false;
		}
		
		/**
		 * Whether we're decorating with the footer or not
		 * @return unknown_type
		 */
		public function doDecorateFooter()
		{
			return ($this->_decoration == self::DECORATE_FOOTER || ($this->_decoration == self::DECORATE_CUSTOM && $this->_decor_footer)) ? true : false;
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

		public function getDecoration()
		{
			return $this->_decoration;
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
			setcookie($key, $value, NOW + $expiration, TBGContext::getTBGPath());
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
			setcookie($key, '', NOW - 36000, TBGContext::getTBGPath());
			return true;
		}		

		/**
		 * Render current headers
		 */
		public function renderHeaders()
		{
			header("HTTP/1.0 ".$this->_http_status);
			/* headers to stop caching in browsers and proxies */
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header("x-tbg-debugid: ".TBGContext::getDebugID());
			header("Content-Type: " . $this->_content_type . "; charset=" . TBGContext::getI18n()->getCharset());

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
		 * Return all active stylesheets
		 *
		 * @return array
		 */
		public function getStylesheets()
		{
			return $this->_stylesheets;
		}

		/**
		 * Return all available feeds
		 *
		 * @return array
		 */
		public function getFeeds()
		{
			return $this->_feeds;
		}

		/**
		 * Clear current headers
		 */
		public function clearHeaders()
		{
			$this->_headers = array();
			
		}

		public function getPredefinedBreadcrumbLinks($type, $project = null)
		{
			$i18n = TBGContext::getI18n();
			$links = array();
			switch ($type)
			{
				case 'main_links':
					$links[] = array('url' => TBGContext::getRouting()->generate('home'), 'title' => $i18n->__('Frontpage'));
					$links[] = array('url' => TBGContext::getRouting()->generate('dashboard'), 'title' => $i18n->__('Personal dashboard'));
					$links[] = array('title' => $i18n->__('Issues'));
					$links[] = array('title' => $i18n->__('Teams'));
					$links[] = array('title' => $i18n->__('Clients'));
					$links = TBGEvent::createNew('core', 'breadcrumb_main_links', null, array(), $links)->trigger()->getReturnList();

					if (TBGContext::getUser()->canAccessConfigurationPage())
					{
						$links[] = array('url' => make_url('configure'), 'title' => $i18n->__('Configure The Bug Genie'));
					}
					$links[] = array('url' => TBGContext::getRouting()->generate('about'), 'title' => $i18n->__('About %sitename%', array('%sitename%' => TBGSettings::getTBGname())));
					$links[] = array('url' => TBGContext::getRouting()->generate('account'), 'title' => $i18n->__('Account details'));

					break;
				case 'project_summary':
					$links[] = array('url' => TBGContext::getRouting()->generate('project_dashboard', array('project_key' => $project->getKey())), 'title' => $i18n->__('Dashboard'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_planning', array('project_key' => $project->getKey())), 'title' => $i18n->__('Planning'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_roadmap', array('project_key' => $project->getKey())), 'title' => $i18n->__('Roadmap'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_team', array('project_key' => $project->getKey())), 'title' => $i18n->__('Team overview'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_statistics', array('project_key' => $project->getKey())), 'title' => $i18n->__('Statistics'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_timeline', array('project_key' => $project->getKey())), 'title' => $i18n->__('Timeline'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_reportissue', array('project_key' => $project->getKey())), 'title' => $i18n->__('Report an issue'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_issues', array('project_key' => $project->getKey())), 'title' => $i18n->__('Issues'));
					$links = TBGEvent::createNew('core', 'breadcrumb_project_links', null, array(), $links)->trigger()->getReturnList();
					$links[] = array('url' => TBGContext::getRouting()->generate('project_settings', array('project_key' => $project->getKey())), 'title' => $i18n->__('Settings'));
					$links[] = array('url' => TBGContext::getRouting()->generate('project_release_center', array('project_key' => $project->getKey())), 'title' => $i18n->__('Release center'));
					break;
				case 'client_list':
					foreach (TBGClient::getAll() as $client)
					{
						if ($client->hasAccess())
							$links[] = array('url' => TBGContext::getRouting()->generate('client_dashboard', array('client_id' => $client->getID())), 'title' => $client->getName());
					}
					break;
				case 'team_list':
					foreach (TBGTeam::getAll() as $team)
					{
						if ($team->hasAccess())
							$links[] = array('url' => TBGContext::getRouting()->generate('team_dashboard', array('team_id' => $team->getID())), 'title' => $team->getName());
					}
					break;
			}

			return $links;
		}
		
		public function getAllHeaders()
		{
			return $this->_headers;
		}

	}
