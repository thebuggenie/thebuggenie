<?php

namespace thebuggenie\core\modules\installation;

use thebuggenie\core\framework;

class Upgrade
{

    protected $upgrade_complete = false;
    protected $current_version;

    protected function _upgradeFrom4dot1()
    {
        set_time_limit(0);

        \thebuggenie\core\entities\tables\Milestones::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_41\Milestone::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.1';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot1()
    {
        set_time_limit(0);

        \thebuggenie\core\modules\installation\upgrade_415\Issue::getB2DBTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_411\Issue::getB2DBTable());
        \thebuggenie\core\modules\installation\upgrade_416\Project::getB2DBTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_411\Project::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.2';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot2()
    {
        set_time_limit(0);

        \thebuggenie\modules\mailing\entities\tables\MailQueueTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_412\MailQueueTable::getTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.3';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot3()
    {
        set_time_limit(0);

        \thebuggenie\modules\agile\entities\tables\AgileBoards::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_413\AgileBoard::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.4';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot4()
    {
        set_time_limit(0);

        \thebuggenie\core\entities\tables\Files::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_414\File::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.5';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot5()
    {
        set_time_limit(0);

        \thebuggenie\core\entities\tables\Issues::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_415\Issue::getB2DBTable());
        \thebuggenie\core\entities\tables\IssueSpentTimes::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_415\IssueSpentTime::getB2DBTable());
        \thebuggenie\core\entities\tables\IssueEstimates::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_415\IssueEstimatesTable::getTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.6';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot6()
    {
        set_time_limit(0);

        \thebuggenie\core\entities\tables\Projects::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_416\Project::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.7';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot7()
    {
        set_time_limit(0);

        \thebuggenie\core\entities\tables\Notifications::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_417\Notification::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.8';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot9()
    {
        set_time_limit(0);

        \thebuggenie\core\entities\tables\NotificationSettings::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_419\NotificationSetting::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.10';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot11()
    {
        set_time_limit(0);

        \thebuggenie\core\entities\tables\ScopeHostnames::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\Notifications::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\WorkflowTransitionValidationRules::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\WorkflowTransitionActions::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\WorkflowStepTransitions::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\Links::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\Log::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\Teams::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\IssueCustomFields::getTable()->createIndexes();
        \thebuggenie\core\entities\tables\ListTypes::getTable()->createIndexes();

        $this->upgrade_complete = true;
        $this->current_version = '4.1.12';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot12()
    {
        set_time_limit(0);

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Upgrading comments table. This may take a few minutes.\n");
        }

        \thebuggenie\core\entities\tables\Comments::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_4112\Comment::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.13';

        if (defined('TBG_CLI')) {
            framework\cli\Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    public function upgrade()
    {
        list ($this->current_version, $this->upgrade_available) = framework\Settings::getUpgradeStatus();

        $scope = new \thebuggenie\core\entities\Scope();
        $scope->setID(1);
        $scope->setEnabled();
        framework\Context::setScope($scope);

        $this->upgrade_complete = false;

        try {
            switch ($this->current_version) {
                case '3.2.0':
                case '3.2':
                    throw new \Exception('Upgrade unavailable. Please upgrade via the web interface');
                case '4.0':
                case '4.1':
                case '4.1.0':
                    $this->_upgradeFrom4dot1();
                case '4.1.1':
                    $this->_upgradeFrom4dot1dot1();
                case '4.1.2':
                    $this->_upgradeFrom4dot1dot2();
                case '4.1.3':
                    $this->_upgradeFrom4dot1dot3();
                case '4.1.4':
                    $this->_upgradeFrom4dot1dot4();
                case '4.1.5':
                    $this->_upgradeFrom4dot1dot5();
                case '4.1.6':
                    $this->_upgradeFrom4dot1dot6();
                case '4.1.7':
                    $this->_upgradeFrom4dot1dot7();
                case '4.1.8':
                case '4.1.9':
                case '4.1.10':
                    $this->_upgradeFrom4dot1dot9();
                case '4.1.11':
                    $this->_upgradeFrom4dot1dot11();
                case '4.1.12':
                    $this->_upgradeFrom4dot1dot12();
                default:
                    $this->upgrade_complete = true;
                    break;
            }
        } catch (\Exception $e) {
            list ($existing_version, ) = framework\Settings::getUpgradeStatus();
            if ($this->current_version != $existing_version) {
                $existing_installed_content = file_get_contents(THEBUGGENIE_PATH . 'installed');
                file_put_contents(THEBUGGENIE_PATH . 'installed', framework\Settings::getVersion(false, true) . ', upgraded ' . date('d.m.Y H:i') . "\n" . $existing_installed_content);
            }

            throw $e;
        }

        if ($this->upgrade_complete)
        {
            $existing_installed_content = file_get_contents(THEBUGGENIE_PATH . 'installed');
            file_put_contents(THEBUGGENIE_PATH . 'installed', framework\Settings::getVersion(false, true) . ', upgraded ' . date('d.m.Y H:i') . "\n" . $existing_installed_content);
            $this->current_version = framework\Settings::getVersion(false, false);

            return true;
        }

        return false;
    }

}
