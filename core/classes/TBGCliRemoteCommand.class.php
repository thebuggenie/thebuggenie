<?php

	/**
	 * CLI remote command class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
			$this->addOptionalArgument('server', 'URL for the remote The Bug Genie install');
			$this->addOptionalArgument('username', "The username to authenticate as");
		}

		final protected function _prepare()
		{
			if ($this->hasProvidedArgument('server'))
			{
				$this->_current_remote_server = $this->getProvidedArgument('server');
			}
			elseif (file_exists(TBGContext::getIncludePath() . '.remote_server'))
			{
				$this->_current_remote_server = file_get_contents(TBGContext::getIncludePath() . '.remote_server');
			}
			else
			{
				throw new Exception("Please specify an installation of The Bug Genie to connect to by running the set_remote command first.");
			}

			if ($this->hasProvidedArgument('username'))
			{
				$this->_current_remote_user = $this->getProvidedArgument('username');
			}
			elseif (file_exists(TBGContext::getIncludePath() . '.remote_username'))
			{
				$this->_current_remote_user = file_get_contents(TBGContext::getIncludePath() . '.remote_username');
			}
			else
			{
				$this->_current_remote_user = TBGContext::getCurrentCLIusername();
			}

			if (file_exists(TBGContext::getIncludePath() . '.remote_password_hash'))
			{
				$this->_current_remote_password_hash = file_get_contents(TBGContext::getIncludePath() . '.remote_password_hash');
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

		protected function getRemoteResponse($url)
		{
			$headers = "Accept-language: en\r\n";
			$headers .= "Cookie: tbg3_username={$this->_getCurrentRemoteUser()}; tbg3_password={$this->_getCurrentRemotePasswordHash()}\r\n";

			$options = array('http' => array('method' => 'GET', 'header' => $headers));

			$retval = file_get_contents($url, false, stream_context_create($options));

			if ($retval === false)
			{
				throw new Exception('An error occurred while retrieving ' . $url . ': ' . join(', ', $http_response_header));
			}
			return json_decode($retval);
		}

		protected function getRemoteURL($route_name, $params = array())
		{
			$url = TBGContext::getRouting()->generate($route_name, $params, true);
			$host = $this->_getCurrentRemoteServer();
			if (substr($host, strlen($host) - 2) == '/') $host = substr($host, 0, strlen($host) - 2);

			return $host . substr($url, 2);
		}

	}