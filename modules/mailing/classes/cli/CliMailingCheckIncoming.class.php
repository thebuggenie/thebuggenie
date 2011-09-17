<?php

/**
 * CLI command class, main -> help
 *
 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
 * @version 3.1
 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
 * @package thebuggenie
 * @subpackage core
 */

/**
 * CLI command class, main -> help
 *
 * @package thebuggenie
 * @subpackage mailing
 */
class CliMailingCheckIncoming extends TBGCliCommand
{

	protected function _setup()
	{
		$this->_command_name = 'check_incoming';
		$this->_description = "Checks all configured mailboxes for new mail";
		$this->addOptionalArgument('status', "Set to 'yes' to only show if there are mails waiting to be processed");
		$this->addOptionalArgument('limit', "Specify a limit to only process a certain number of emails");
	}

	public function do_execute()
	{
		$this->cliEcho("Checking for emails ... \n", 'white', 'bold');
		$this->getModule()->processIncomingEmails();
		$this->cliEcho("Done!\n");
	}

}