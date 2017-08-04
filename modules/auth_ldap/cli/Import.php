<?php

namespace thebuggenie\modules\auth_ldap\cli;
use thebuggenie\core\framework;

/**
 * Implementation of CLI command for importing LDAP users into TBG.
 *
 * @author Branko Majic <branko@majic.rs>
 * @version 4.2
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage auth_ldap
 */


/**
 * CLI command for performing import of LDAP users into TBG.
 *
 * @package thebuggenie
 * @subpackage auth_ldap
 */
class Import extends \thebuggenie\core\framework\cli\Command
{
    const ERROR = 1;

    /**
     * Sets-up the command name and description.
     */
    protected function _setup()
    {
        $this->_command_name = 'import';
        $this->_description = 'Import new and update existing users based on user information from LDAP directory';
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
            $statistics = framework\Context::getModule('auth_ldap')->importAndUpdateUsers();
        }
        catch (\Exception $e)
        {
            $this->cliEcho($i18n->__("Import failed") . ": " . $e->getMessage(), 'red');
            $this->cliEcho("\n");
            exit(self::ERROR);
        }

        $this->cliEcho($i18n->__('Import successful! Imported %imported users and updated %updated users out of total %total valid users found in LDAP',
                                 ['%imported' => $statistics['imported'],
                                  '%updated' => $statistics['updated'],
                                  '%total' => $statistics['total']]));
        $this->cliEcho("\n");
    }
}