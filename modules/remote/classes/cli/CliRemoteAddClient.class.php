<?php

	/**
	 * CLI command class, remote -> add_client
	 *
	 * @author Jonathan Baugh <jbaugh@saleamp.com>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, remote -> add_client
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliRemoteAddClient extends TBGCliRemoteCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'add_client';
			$this->_description = "Create a new client on a remote server";
			$this->addRequiredArgument('client_name', 'The client name for the client you want to create');
			parent::_setup();
		}

		public function do_execute()
		{
			$this->cliEcho('Creating client ');
			$this->cliEcho($this->getProvidedArgument('client_name') . "\n\n", 'green');
			
			$url_options = array('format'=>'json');
			$post_data   = array('client_name'=>$this->getProvidedArgument('client_name'));
			
			try {
				$response = $this->getRemoteResponse($this->getRemoteURL('configure_users_add_client', $url_options), $post_data);
				
				if( is_object($response) ) {
					if( !isset($response->error) ) {
						$this->cliEcho('Success.', 'green');
					} else {
						throw new Exception($response->error);
					}
				}
			} catch (Exception $e) {
				$this->cliEcho($e->getMessage(), 'red');
			}
			
			$this->cliEcho("\n");
		}

	}