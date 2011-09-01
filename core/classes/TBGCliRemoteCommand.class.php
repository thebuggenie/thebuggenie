<?php

	/**
	 * CLI remote command class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI remote command class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	abstract class TBGCliRemoteCommand extends TBGCliCommand
	{

		protected $_current_remote_server = null;

		protected $_current_remote_user = null;

		protected $_current_remote_password_hash = null;

		protected function _setup()
		{
			if (!ini_get('allow_url_fopen'))
			{
				$this->cliEcho("The php.ini directive ", 'yellow');
				$this->cliEcho("allow_url.fopen", 'yellow', 'bold');
				$this->cliEcho(" is not set to 1\n", 'yellow');
				$this->cliEcho("Trying to set correct value for the current run ...");
				ini_set('allow_url_fopen', 1);
				if (!ini_get('allow_url_fopen'))
				{
					throw new Exception('Could not set "allow_url_fopen" to correct value. Please fix your cli configuration.');
				}
				else
				{
					$this->cliEcho('OK', 'green', 'bold');
					$this->cliEcho("\n\n");
				}
			}
			$this->addOptionalArgument('server', 'URL for the remote The Bug Genie install');
			$this->addOptionalArgument('username', "The username to authenticate as");
		}

		final protected function _prepare()
		{
			if ($this->hasProvidedArgument('server'))
			{
				$this->_current_remote_server = $this->getProvidedArgument('server');
			}
			elseif (file_exists(THEBUGGENIE_CONFIG_PATH . '.remote_server'))
			{
				$this->_current_remote_server = file_get_contents(THEBUGGENIE_CONFIG_PATH . '.remote_server');
			}
			else
			{
				throw new Exception("Please specify an installation of The Bug Genie to connect to by running the set_remote command first.");
			}

			if ($this->hasProvidedArgument('username'))
			{
				$this->_current_remote_user = $this->getProvidedArgument('username');
			}
			elseif (file_exists(THEBUGGENIE_CONFIG_PATH . '.remote_username'))
			{
				$this->_current_remote_user = file_get_contents(THEBUGGENIE_CONFIG_PATH . '.remote_username');
			}
			else
			{
				$this->_current_remote_user = TBGContext::getCurrentCLIusername();
			}

			if (file_exists(THEBUGGENIE_CONFIG_PATH . '.remote_password_hash'))
			{
				$this->_current_remote_password_hash = file_get_contents(THEBUGGENIE_CONFIG_PATH . '.remote_password_hash');
			}
			else
			{
				$this->cliEcho('Please enter the password for user ');
				$this->cliEcho($this->_getCurrentRemoteUser(), 'white', 'bold');
				$this->cliEcho(' (the password will not be stored): ');
				$this->_current_remote_password_hash = TBGUser::hashPassword($this->_getCliInput());
			}

		}

		protected function _getCurrentRemoteServer()
		{
			return $this->_current_remote_server;
		}

		protected function _getCurrentRemoteUser()
		{
			return $this->_current_remote_user;
		}

		protected function _getCurrentRemotePasswordHash()
		{
			return $this->_current_remote_password_hash;
		}

		protected function getRemoteResponse($url, $postdata = array())
		{
			$headers = "Accept-language: en\r\n";
			$headers .= "Cookie: tbg3_username={$this->_getCurrentRemoteUser()}; tbg3_password={$this->_getCurrentRemotePasswordHash()}\r\n";

			$options = array('http' => array('method' => (empty($postdata)) ? 'GET' : 'POST', 'header' => $headers));

			if (!empty($postdata))
			{
				$content = '';
				$boundary = "---------------------".mb_substr(md5(rand(0,32000)), 0, 10); 
				$headers .= 'Content-Type: multipart/form-data; boundary='.$boundary."\r\n";
				$options['http']['header'] = $headers;

				foreach($postdata as $key => $val) 
				{ 
					$content .= "--$boundary\n";
					$content .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n";
				} 

				$content .= "--$boundary\n";
				$options['http']['content'] = $content;
			}

			$prev_rep_val = error_reporting();
			error_reporting(E_USER_ERROR);
			$retval = file_get_contents($url, false, stream_context_create($options));
			error_reporting(E_ERROR);

			if ($retval === false)
			{
				$errors = (isset($http_response_header)) ? ":\n" . $http_response_header[0] : '';
				throw new Exception($url . " could not be retrieved" . $errors);
			}
			$response = json_decode($retval);
			if (is_object($response) && isset($response->failed) && $response->failed)
			{
				throw new Exception($url . "\n" . $response->message);
			}

			if (!is_object($response) && !is_array($response))
				throw new Exception('Could not parse the return value from the server. Please re-check the command being executed.');
			
			return $response;
		}

		protected function getRemoteURL($route_name, $params = array())
		{
			$url = TBGContext::getRouting()->generate($route_name, $params, true);
			$host = $this->_getCurrentRemoteServer();
			if (mb_substr($host, mb_strlen($host) - 2) != '/') $host .= '/';

			return $host . mb_substr($url, 2);
		}

	}
