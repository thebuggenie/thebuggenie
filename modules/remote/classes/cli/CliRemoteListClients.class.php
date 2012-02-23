<?php

	/**
	 * CLI command class, remote -> list_clients
	 *
	 * Copied from CliRemoteListProjects.class.php and modified for clients.
	 *
	 * @author Jonathan Baugh <jbaugh@saleamp.com>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, remote -> list_clients
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliRemoteListClients extends TBGCliRemoteCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'list_clients';
			$this->_description = "Query a remote server for a list of available clients";
			parent::_setup();
		}

		public function do_execute()
		{
			$this->cliEcho('Querying ');
			$this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
			$this->cliEcho(" for list of clients ...\n\n");

			$response = $this->getRemoteResponse($this->getRemoteURL('list_clients', array('format' => 'json')));

			if (!empty($response))
			{
				$this->cliEcho("client_key", 'green', 'bold');
				$this->cliEcho(" - client name\n", 'white', 'bold');
				foreach ($response as $client_key => $client_name)
				{
					$this->cliEcho($client_key, 'green');
					$this->cliEcho(" - $client_name\n");
				}
				$this->cliEcho("\n");
			}
			else
			{
				$this->cliEcho("No clients available.\n\n");
			}
		}

	}
