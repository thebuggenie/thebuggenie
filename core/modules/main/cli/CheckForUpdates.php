<?php

namespace thebuggenie\core\modules\main\cli;

use thebuggenie\core\framework;

/**
 * Implementation of CLI command for checking if TBG is up-to-date.
 *
 * @author Branko Majic <branko@majic.rs>
 * @version 4.2
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage core
 */

/**
 * CLI command for checking if TBG is up-to-date.
 *
 * @package thebuggenie
 * @subpackage core
 */
class CheckForUpdates extends \thebuggenie\core\framework\cli\Command
{
    const OUTDATED = 1;
    const ERROR = 2;

    protected function _setup()
    {
        $this->_command_name = 'check_for_updates';
        $this->_description = "Checks if newer version is available for upgrade.";
    }

    public function do_execute()
    {
        $update_check = framework\Context::checkForUpdates();

        if ($update_check["uptodate"] === null)
        {
            $this->cliEcho($update_check["title"], "red", "bold");
            $this->cliEcho("\n");
            $this->cliEcho($update_check["message"]);
            $this->cliEcho("\n");
            exit(self::ERROR);
        }
        elseif ($update_check["uptodate"] === false)
        {
            $this->cliEcho($update_check["title"], "yellow", "bold");
            $this->cliEcho("\n");
            $this->cliEcho($update_check["message"]);
            $this->cliEcho("\n");
            exit(self::OUTDATED);
        }
        elseif ($update_check["uptodate"] === true)
        {
            $this->cliEcho($update_check["title"], "green", "bold");
            $this->cliEcho("\n");
            $this->cliEcho($update_check["message"]);
            $this->cliEcho("\n");
        }
    }
}