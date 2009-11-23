<?php

	/**
	 * Request class, used for retrieving request information
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mvc
	 */

	/**
	 * Request class, used for retrieving request information
	 *
	 * @package thebuggenie
	 * @subpackage mvc
	 */
	class BUGSrequest
	{
		
		protected $_request_parameters = array();
		protected $_post_parameters = array();
		protected $_get_parameters = array();
		protected $_cookies = array();
		
		protected $_is_ajax_call = false;
		
		const POST = 1;
		const GET = 2; 
		
		/**
		 * Handles an uploaded file, stores it to the correct folder, adds an entry
		 * to the database and returns a BUGSfile object
		 * 
		 * @param string $thefile The request parameter the file was sent as
		 * 
		 * @return BUGSfile The BUGSfile object
		 */
		static function handleUpload($thefile)
		{
			if (BUGSsettings::get('enable_uploads'))
			{
				if ($thefile['error'] == UPLOAD_ERR_OK)
				{
					if (filesize($thefile['tmp_name']) > (BUGSsettings::get('max_file_size') * 1024 * 1024))
					{
						throw new Exception(__('You cannot upload files bigger than %max_size% MB', array('%max_size%' => BUGSsettings::get('max_file_size'))));
					}
					$extension = substr(basename($thefile['name']), strpos(basename($thefile['name']), '.'));
					if ($extension == '')
					{
						throw new Exception(__('Could not determine filetype'));
					}
					else
					{
						$extension = substr($extension, 1);
						$upload_extensions = explode(',', BUGSsettings::get('uploads_filetypes'));
						if (BUGSsettings::get('uploads_blacklist'))
						{
							foreach ($upload_extensions as $an_ext)
							{
								if (strtolower(trim($extension)) == strtolower(trim($an_ext)))
								{
									throw new Exception(__('This filetype is not allowed'));
								}
							}
						}
						else
						{
							$is_ok = false;
							foreach ($upload_extensions as $an_ext)
							{
								if (strtolower(trim($extension)) == strtolower(trim($an_ext)))
								{
									$is_ok = true;
									break;
								}
							}
							if (!$is_ok) throw new Exception(__('This filetype is not allowed'));
						}
						if (in_array(strtolower(trim($extension)), array('php', 'asp')))
						{
							throw new Exception(__('This filetype is not allowed'));
						}
					}
					if (is_uploaded_file($thefile['tmp_name']))
					{
						$files_dir = self::getIncludePath() . 'files/';
						$new_filename = self::getUser()->getID() . '_' . $_SERVER["REQUEST_TIME"] . '_' . basename($thefile['name']);
						if (!move_uploaded_file($thefile['tmp_name'], $files_dir . $new_filename))
						{
							throw new Exception(__('An error occured when saving the file'));
						}
						else
						{
							return $new_filename;
						}
					}
					else
					{
						throw new Exception(__('The file was not uploaded correctly'));
					}
				}
				else
				{
					switch ($thefile['error'])
					{
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							throw new Exception(__('You cannot upload files bigger than %max_size% MB', array('%max_size%' => BUGSsettings::get('max_file_size'))));
							break;
						case UPLOAD_ERR_PARTIAL:
							throw new Exception(__('The upload was interrupted, please try again'));
							break;
						case UPLOAD_ERR_NO_FILE:
							throw new Exception(__('No file was uploaded'));
							break;
						default:
							throw new Exception(__('An unhandled error occured') . ': ' . $thefile['error']);
							break;
					}
				}
			}
			else
			{
				throw new Exception(__('You are not allowed to upload files'));
			}
			throw new Exception(__('The file could not be uploaded'));
		}

		/**
		 * Sanitizes a given parameter and returns it
		 *
		 * @param mixed $params
		 * 
		 * @return mixed
		 */
		protected function __sanitize_params($params)
		{
			if (is_array($params))
			{
				foreach ($params as $key => $param)
				{
					if (is_string($param))
					{
						$params[$key] = $this->__sanitize_string($param);
					}
					elseif (is_array($param))
					{
						$params[$key] = $this->__sanitize_params($param);
					}
				}
			}
			elseif (is_string($params))
			{
				$params = $this->__sanitize_string($params);
			}
			return $params;
		}

		/**
		 * Sets up the BUGSrequest object and initializes and assigns the correct
		 * variables
		 */
		public function __construct()
		{
			foreach ($_COOKIE as $key => $value)
			{
				$this->_cookies[$key] = $value;
			}
			foreach ($_POST as $key => $value)
			{
				$this->_post_parameters[$key] = $value;
				$this->_request_parameters[$key] = $value;
			}
			foreach ($_GET as $key => $value)
			{
				$this->_get_parameters[$key] = $value;
				$this->_request_parameters[$key] = $value;
			}
			$this->_is_ajax_call = (array_key_exists("HTTP_X_REQUESTED_WITH", $_SERVER) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest');
		}

		/**
		 * Get a parameter from the request
		 *
		 * @param string $key The parameter you want to retrieve
		 * @param mixed $default_value The value to return if it doesn't exist
		 * @param boolean $sanitized Whether to sanitize strings or not
		 *
		 * @return mixed
		 */
		public function getParameter($key, $default_value = null, $sanitized = true)
		{
			if (isset($this->_request_parameters[$key]))
			{
				if ($sanitized && is_string($this->_request_parameters[$key]))
				{
					return $this->__sanitize_string($this->_request_parameters[$key]);
				}
				elseif (is_string($this->_request_parameters[$key]))
				{
					return $this->__strip_if_needed($this->_request_parameters[$key]);
				}
				elseif ($sanitized)
				{
					return $this->__sanitize_params($this->_request_parameters[$key]);
				}
				else
				{
					return $this->_request_parameters[$key];
				}
			}
			else
			{
				return $default_value;
			}
		}

		/**
		 * Retrieve an unsanitized request parameter
		 *
		 * @see getParameter
		 *
		 * @param string $key The parameter you want to retrieve
		 * @param mixed $default_value[optional] The value to return if it doesn't exist
		 *
		 * @return mixed
		 */
		public function getRawParameter($key, $default_value = null)
		{
			return $this->getParameter($key, $default_value, false);
		}

		/**
		 * Retrieve a cookie
		 *
		 * @param string $key The cookie to retrieve
		 * @param mixed $default_value The value to return if it doesn't exist
		 *
		 * @return mixed
		 */
		public function getCookie($key, $default_value = null)
		{
			return (isset($this->_cookies[$key])) ? $this->_cookies[$key] : $default_value;
		}

		/**
		 * Check to see if a request parameter is set
		 *
		 * @param string $key The parameter to check for
		 *
		 * @return boolean
		 */
		public function hasParameter($key)
		{
			return array_key_exists($key, $this->_request_parameters);
		}
		
		/**
		 * Check to see if a cookie is set
		 *
		 * @param string $key The cookie to check for
		 *
		 * @return boolean
		 */
		public function hasCookie($key)
		{
			return (bool) ($this->getCookie($key) !== null);
		}

		/**
		 * Set a request parameter
		 *
		 * @param string $key The parameter to set
		 * @param mixed $value The value to set it too
		 */
		public function setParameter($key, $value)
		{
			$this->_request_parameters[$key] = $value;
		}

		/**
		 * Get the current request method
		 *
		 * @return integer
		 */
		public function getMethod()
		{
			switch (strtolower($_SERVER['REQUEST_METHOD']))
			{
				case 'get':
					return self::GET;
					break;
				case 'post':
					return self::POST;
					break; 
			}			
		}
		
		/**
		 * Check if the current request method is $method
		 * 
		 * @param $method
		 * 
		 * @return boolean
		 */
		public function isMethod($method)
		{
			return ($this->getMethod() == $method) ? true : false;
		}

		/**
		 * Check if the current request is an ajax call
		 *
		 * @return boolean
		 */
		public function isAjaxCall()
		{
			return $this->_is_ajax_call;
		}

		/**
		 * Strip slashes for a string if magic quotes gpc is turned on in phpini
		 *
		 * @param string $string The string to strip
		 *
		 * @return string a slashstripped string
		 */
		protected function __strip_if_needed($string)
		{
			if (get_magic_quotes_gpc() == 1)
			{
				if (is_array($string))
				{
					throw new Exception('peeeeka!');
					var_dump($string);die();
				}
				$string = stripslashes($string);
			}
			return $string;
		}

		/**
		 * Sanitize a string
		 *
		 * @param string $string The string to sanitize
		 *
		 * @return string the sanitized string
		 */
		protected function __sanitize_string($string)
		{
			$string = $this->__strip_if_needed($string);
			$charset = (class_exists('BUGScontext')) ? BUGScontext::getI18n()->getCharset() : 'utf-8';
			return htmlspecialchars($string, ENT_QUOTES, $charset);
		}
		
		/**
		 * Wrapper around __sanitize_string method
		 *
		 * @param string $string The string to sanitize
		 *
		 * @return string the sanitized string
		 */
		public function sanitize_input($string)
		{
			return $this->__sanitize_string($string);
		}
	}
