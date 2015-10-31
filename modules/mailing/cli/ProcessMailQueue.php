<?php

    namespace thebuggenie\modules\mailing\cli;

    use thebuggenie\modules\mailing\entities\tables\MailQueueTable;

    /**
     * CLI command class, main -> help
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> help
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ProcessMailQueue extends \thebuggenie\core\framework\cli\Command
    {
        
        protected function _setup()
        {
            $this->_command_name = 'process_mail_queue';
            $this->_description = "Processes emails waiting to be sent";
            $this->addOptionalArgument('test', "Set to 'yes' or 'no' to do a test run");
            $this->addOptionalArgument('limit', "Specify a limit to only process a certain number of emails");
            $this->setScoped();
        }

        public function do_execute()
        {

            $mailing = \thebuggenie\core\framework\Context::getModule('mailing');
            if (!$mailing->isOutgoingNotificationsEnabled())
            {
                $this->cliEcho("Outgoing email notifications are disabled.\n", 'red', 'bold');
                $this->cliEcho("\n");
                return;
            }
            if (!$mailing->getMailingUrl())
            {
                $this->cliEcho("You must configure the mailing url via the web interface before you can use this feature.\n", 'red', 'bold');
                $this->cliEcho("\n");
                return;
            }

            $this->cliEcho("Processing mail queue ... \n", 'white', 'bold');
            $limit = $this->getProvidedArgument('limit', null);
            $messages = MailQueueTable::getTable()->getQueuedMessages($limit);

            $this->cliEcho("Email(s) to process: ");
            $this->cliEcho(count($messages)."\n", 'white', 'bold');

            if ($this->getProvidedArgument('test', 'no') == 'no')
            {
                if (count($messages) > 0)
                {
                    $processed_messages = array();
                    $failed_messages = 0;
                    try
                    {
                        foreach ($messages as $message_id => $message)
                        {
                            $retval = $mailing->getMailer()->send($message);
                            $processed_messages[] = $message_id;
                            if (!$retval) $failed_messages++;
                        }
                    }
                    catch (\Exception $e) { throw $e; }

                    if (count($processed_messages))
                    {
                        MailQueueTable::getTable()->deleteProcessedMessages($processed_messages);
                        $this->cliEcho("Emails successfully processed: ");
                        $this->cliEcho(count($messages)."\n", 'green', 'bold');
                        if ($failed_messages > 0)
                        {
                            $this->cliEcho("Emails processed with error(s): ");
                            $this->cliEcho($failed_messages."\n", 'red', 'bold');
                        }
                    }
                }
            }
            else
            {
                $this->cliEcho("Not processing queue...\n");
            }
        }

    }
