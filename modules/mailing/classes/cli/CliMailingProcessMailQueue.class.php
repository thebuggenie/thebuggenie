<?php

	/**
	 * CLI command class, main -> help
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> help
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMailingProcessMailQueue extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'process_mail_queue';
			$this->addOptionalArgument('test', "Set to 'yes' or 'no' to do a test run");
			$this->addOptionalArgument('limit', "Specify a limit to only process a certain number of emails");
		}

		public function getDescription()
		{
			return "Processes emails in the mailing queue";
		}

		public function do_execute()
		{
			$this->cliEcho("Processing mail queue ... \n", 'white', 'bold');
			$limit = $this->getProvidedArgument('limit', null);
			$messages = TBGMailQueueTable::getTable()->getQueuedMessages($limit);

			$this->cliEcho("Email(s) to process: ");
			$this->cliEcho(count($messages)."\n", 'white', 'bold');

			if ($this->getProvidedArgument('test', 'no') == 'no')
			{
				if (count($messages) > 0)
				{
					$mailer = TBGMailing::getModule()->getMailer();
					$processed_messages = array();
					try
					{
						foreach ($messages as $message_id => $message)
						{
							$mailer->send($message);
							$processed_messages[] = $message_id;
						}
					}
					catch (Exception $e) { throw $e; }

					if (count($processed_messages))
					{
						TBGMailQueueTable::getTable()->deleteProcessedMessages($processed_messages);
						$this->cliEcho("Emails successfully processed: ");
						$this->cliEcho(count($messages)."\n", 'green', 'bold');
					}
				}
			}
			else
			{
				$this->cliEcho("Not processing queue...\n");
			}
		}

	}