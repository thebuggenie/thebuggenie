<?php

    namespace thebuggenie\modules\mailing\cli;

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
     * @subpackage mailing
     */
    class CheckIncoming extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'check_incoming';
            $this->_description = "Checks all configured mailboxes for new mail";
            $this->addOptionalArgument('status', "Set to 'yes' to only show if there are mails waiting to be processed");
            $this->addOptionalArgument('limit', "Specify a limit to only process a certain number of emails (default 25)");
            $this->setScoped();
        }

        public function do_execute()
        {
            $this->cliEcho("Checking for emails ... \n", 'white', 'bold');
            $limit = $this->getProvidedArgument('limit', 25);
            $accounts = $this->getModule()->getIncomingEmailAccounts();

            if (count($accounts))
            {
                $this->cliEcho("\n");
                foreach ($accounts as $account)
                {
                    $account->connect();
                    $unread_count = $account->getUnreadCount();
                    $this->cliEcho("[".$account->getProject()->getKey()." (" . $account->getName() . ")] Processing ({$unread_count} unprocessed)\n");
                    if ($unread_count > 0)
                    {
                        $this->cliEcho("[".$account->getProject()->getKey()." (" . $account->getName() . ")] Will process up to {$limit} emails from this account\n");
                        $this->getModule()->processIncomingEmailAccount($account, $limit);
                        $this->cliEcho("[".$account->getProject()->getKey()." (" . $account->getName() . ")] Processed ".$account->getNumberOfEmailsLastFetched()." emails\n");
                    }
                    else
                    {
                        $this->cliEcho("[".$account->getProject()->getKey()." (" . $account->getName() . ")] Nothing to do for this account\n");
                    }
                    $account->disconnect();
                    $this->cliEcho("\n");
                }
            }
            else
            {
                $this->cliEcho("No incoming email accounts configured!\n");
            }
            $this->cliEcho("Done!\n");
        }

    }
