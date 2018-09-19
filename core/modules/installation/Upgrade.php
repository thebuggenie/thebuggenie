<?php

namespace thebuggenie\core\modules\installation;

use thebuggenie\core\entities\Datatype;
use thebuggenie\core\entities\Issuetype;
use thebuggenie\core\entities\IssuetypeScheme;
use thebuggenie\core\entities\Scope;
use thebuggenie\core\entities\tables\Files;
use thebuggenie\core\entities\tables\IssueSpentTimes;
use thebuggenie\core\entities\tables;
use thebuggenie\core\entities\tables\Users;
use thebuggenie\core\entities\tables\UserSessions;
use thebuggenie\core\entities\Workflow;
use thebuggenie\core\entities\WorkflowScheme;
use thebuggenie\core\framework;
use thebuggenie\core\framework\cli\Command;
use thebuggenie\core\modules\installation\upgrade_4112\UsersTable;
use thebuggenie\modules\publish\entities\tables\Articles;

class Upgrade
{

    protected $upgrade_complete = false;
    protected $upgrade_options = [];
    protected $current_version;

    protected function _upgradeFrom4dot1()
    {
        \thebuggenie\core\entities\tables\Milestones::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_41\Milestone::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.1';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot1()
    {
        \thebuggenie\core\modules\installation\upgrade_415\Issue::getB2DBTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_411\Issue::getB2DBTable());
        \thebuggenie\core\modules\installation\upgrade_416\Project::getB2DBTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_411\Project::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.2';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot2()
    {
        \thebuggenie\modules\mailing\entities\tables\MailQueueTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_412\MailQueueTable::getTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.3';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot3()
    {
        \thebuggenie\modules\agile\entities\tables\AgileBoards::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_413\AgileBoard::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.4';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot4()
    {
        \thebuggenie\core\entities\tables\Files::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_414\File::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.5';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot5()
    {
        \thebuggenie\core\entities\tables\Issues::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_415\Issue::getB2DBTable());
        \thebuggenie\core\entities\tables\IssueSpentTimes::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_415\IssueSpentTime::getB2DBTable());
        \thebuggenie\core\entities\tables\IssueEstimates::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_415\IssueEstimatesTable::getTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.6';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot6()
    {
        \thebuggenie\core\entities\tables\Projects::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_416\Project::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.7';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot7()
    {
        \thebuggenie\core\entities\tables\Notifications::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_417\Notification::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.8';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot9()
    {
        \thebuggenie\core\entities\tables\NotificationSettings::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_419\NotificationSetting::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.10';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot11()
    {
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
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    protected function _upgradeFrom4dot1dot12()
    {
        if (defined('TBG_CLI')) {
            Command::cli_echo("Upgrading comments table. This may take a few minutes.\n");
        }

        \thebuggenie\core\entities\tables\Comments::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_4112\Comment::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.13';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    /**
     * Gather information for the upgrade from versions <= 4.1.13
     *
     * @param framework\Request|null $request
     */
    protected function _prepareUpgradeFrom4dot1dot13(framework\Request $request = null)
    {
        $admin_username = UsersTable::getTable()->getAdminUsername();

        if (defined('TBG_CLI')) {
            Command::cli_echo("\n");
            Command::cli_echo("We're continuously adjusting and improving user security. As a result, this version ");
            Command::cli_echo("changes the way passwords are handled and stored.\n", Command::COLOR_WHITE, Command::STYLE_UNDERLINE);
            Command::cli_echo("All users will require password resets after the upgrade process, and application-specific passwords must be regenerated.\n\n");

            Command::cli_echo("Because of the improved password handling, we need to set a password for the admin account");
            Command::cli_echo(" {$admin_username}\n", Command::COLOR_WHITE, Command::STYLE_BOLD);
            $admin_password = '';
            while ($admin_password == '' || strlen($admin_password) < 8) {
                Command::cli_echo("New password for user with username");
                Command::cli_echo(" {$admin_username}", Command::COLOR_WHITE, Command::STYLE_BOLD);
                Command::cli_echo(" (min 8 characters): ");
                $admin_password = trim(Command::getUserInput());
            }
        } else {
            $admin_password = trim($request['admin_password']);
            if (strlen($admin_password) < 8) {
                throw new \Exception('Please enter a password with atleast 8 characters');
            }
        }

        $this->upgrade_options['4_1_13'] = [
            'admin_username' => $admin_username,
            'admin_password' => $admin_password
        ];
    }

    protected function _upgradeFrom4dot1dot13()
    {
        if (defined('TBG_CLI')) {
            Command::cli_echo("Updating/fixing status of milestones.\n");
        }

        UserSessions::getTable()->create();

        $milestones = \thebuggenie\core\entities\tables\Milestones::getTable()->selectAll();

        foreach ($milestones as $milestone)
        {
            $milestone->updateStatus();
            $milestone->save();
        }

        if (defined('TBG_CLI')) {
            Command::cli_echo("Updating/fixing article types.\n");
        }

        Articles::getTable()->fixArticleTypes();

        if (defined('TBG_CLI')) {
            Command::cli_echo("Fixing file scopes.\n");
        }
        Files::getTable()->fixScopes();

        if (defined('TBG_CLI')) {
            Command::cli_echo("Fixing issue spent times scopes.\n");
        }
        IssueSpentTimes::getTable()->fixScopes();

        $scopes = Scope::getAll();
        $cc = 1;
        if (defined('TBG_CLI')) {
            Command::cli_echo("Implementing new workflows.\n");
        }
        $prev_percentage = 0;
        foreach ($scopes as $scope) {
            if (defined('TBG_CLI')) {
                $percentage = floor(($cc / count($scopes)) * 100);
                if ($percentage != $prev_percentage && $percentage % 5 == 0) {
                    $prev_percentage = $percentage;
                    echo $percentage . '%' . "\n";
                }
            }
            list($bug_report_id, $feature_req_id, $enhancement_id, $task_id, $user_story_id, $idea_id, $epic_id) = Issuetype::getDefaultItems($scope);
            list($full_range_scheme, $balanced_scheme, $balanced_agile_scheme, $simple_scheme) = IssuetypeScheme::loadFixtures($scope, [$bug_report_id, $feature_req_id, $enhancement_id, $task_id, $user_story_id, $idea_id, $epic_id]);
            tables\IssueFields::getTable()->loadFixtures($scope, $full_range_scheme, $balanced_scheme, $balanced_agile_scheme, $simple_scheme, $bug_report_id, $feature_req_id, $enhancement_id, $task_id, $user_story_id, $idea_id, $epic_id);
            Datatype::loadFixtures($scope);

            // Set up workflows
            list ($multi_team_workflow, $balanced_workflow, $simple_workflow) = Workflow::loadFixtures($scope);
            list ($multi_team_workflow_scheme, $balanced_workflow_scheme, $simple_workflow_scheme) = WorkflowScheme::loadFixtures($scope);

            tables\WorkflowIssuetype::getTable()->loadFixtures($scope, $multi_team_workflow, $multi_team_workflow_scheme);
            tables\WorkflowIssuetype::getTable()->loadFixtures($scope, $balanced_workflow, $balanced_workflow_scheme);
            tables\WorkflowIssuetype::getTable()->loadFixtures($scope, $simple_workflow, $simple_workflow_scheme);

            gc_collect_cycles();
            $cc++;
        }

        $admin_user = Users::getTable()->getByUsername($this->upgrade_options['4_1_13']['admin_username']);
        $admin_user->setPassword($this->upgrade_options['4_1_13']['admin_password']);
        $admin_user->save();

        $this->upgrade_complete = true;
        $this->current_version = '4.2.0';

        if (defined('TBG_CLI')) {
            Command::cli_echo("Successfully upgraded to version {$this->current_version}\n");
        }
    }

    /**
     * Perform the actual upgrade
     *
     * @param framework\Request|null $request
     * @return bool
     * @throws \Exception
     */
    public function upgrade(framework\Request $request = null)
    {
        set_time_limit(0);

        list ($this->current_version, $this->upgrade_available) = framework\Settings::getUpgradeStatus();

        $scope = new \thebuggenie\core\entities\Scope();
        $scope->setID(1);
        $scope->setEnabled();
        framework\Context::setScope($scope);

        $this->upgrade_complete = false;

        try {
            if (defined('TBG_CLI')) {
                Command::cli_echo("Gathering information before upgrading...\n");
            }

            switch ($this->current_version) {
                case '4.1.13':
                case '4.1.14':
                    $this->_prepareUpgradeFrom4dot1dot13($request);
                    break;
            }

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
                case '4.1.13':
                case '4.1.14':
                    $this->_upgradeFrom4dot1dot13();
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
