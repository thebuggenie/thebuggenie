<?php

namespace thebuggenie\modules\auth_ldap\cli;
use thebuggenie\core\framework;

/**
 * Implementation of CLI command for pruning users missing in LDAP directory
 * from TBG.
 *
 * @author Branko Majic <branko@majic.rs>
 * @version 4.2
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage auth_ldap
 */


/**
 * CLI command for pruning TBG users.
 *
 * @package thebuggenie
 * @subpackage auth_ldap
 */
class Prune extends \thebuggenie\core\framework\cli\Command
{
    const ERROR = 1;

    /**
     * Sets-up the command name and description.
     */
    protected function _setup()
    {
        $this->_command_name = 'prune';
        $this->_description = 'Remove all users from The Bug Genie that do not exist in LDAP directory. WARNING: This is a very dangerous operation, make sure you are confident in LDAP configuration before proceeding!';
    }

    /**
     * Executes the command.
     *
     */
    public function do_execute()
    {
        $i18n = framework\Context::getI18n();

        try
        {
            $statistics = framework\Context::getModule('auth_ldap')->pruneUsers();
        }
        catch (\Exception $e)
        {
            $this->cliEcho($i18n->__("Pruning failed") . ": " . $e->getMessage(), 'red');
            $this->cliEcho("\n");
            exit(self::ERROR);
        }


        $this->cliEcho($i18n->__('Pruning successful! %deleted users deleted',
                                 ['%deleted' => $statistics['deleted']]));
        $this->cliEcho("\n");
    }
}