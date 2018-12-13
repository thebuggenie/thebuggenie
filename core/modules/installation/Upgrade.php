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
use thebuggenie\modules\mailing\entities\tables\IncomingEmailAccounts;
use thebuggenie\modules\publish\entities\tables\Articles;

class Upgrade
{

    protected $upgrade_complete = false;
    protected $upgrade_options = [];
    protected $current_version;

    protected function cliEchoUpgradeTable($table, $time_warning = false)
    {
        if (!framework\Context::isCLI()) {
            return;
        }

        $namespaces = explode('\\', get_class($table));
        $classname = array_pop($namespaces);
        Command::cli_echo('Upgrading', 'white', 'bold');
        Command::cli_echo(' table ' . implode('\\', $namespaces) . '\\');
        Command::cli_echo($classname, 'yellow');
        if ($time_warning) {
            Command::cli_echo(' - data migration may take a little while ...');
        }
        Command::cli_echo("\n");
    }

    protected function cliEchoCreateTable($table)
    {
        if (!framework\Context::isCLI()) {
            return;
        }

        $namespaces = explode('\\', get_class($table));
        $classname = array_pop($namespaces);
        Command::cli_echo('Creating', 'white', 'bold');
        Command::cli_echo(' table ' . implode('\\', $namespaces) . '\\');
        Command::cli_echo($classname, 'yellow');
        Command::cli_echo("\n");
    }

    protected function cliEchoAddIndexTable($table)
    {
        if (!framework\Context::isCLI()) {
            return;
        }

        $namespaces = explode('\\', get_class($table));
        $classname = array_pop($namespaces);
        Command::cli_echo('Adding indexes', 'white', 'bold');
        Command::cli_echo(' for table ' . implode('\\', $namespaces) . '\\');
        Command::cli_echo($classname, 'yellow');
        Command::cli_echo("\n");
    }

    protected function cliEchoUpgradedVersion($version_number)
    {
        if (!framework\Context::isCLI()) {
            return;
        }

        Command::cli_echo("Successfully upgraded to version ");
        Command::cli_echo($version_number, 'green', 'bold');
        Command::cli_echo("\n");
    }

    protected function _upgradeFrom4dot1()
    {
        $milestones_table = tables\Milestones::getTable();
        $this->cliEchoUpgradeTable($milestones_table);
        $milestones_table->upgrade(upgrade_41\Milestone::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.1';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot1()
    {
        $issues_table = upgrade_415\Issue::getB2DBTable();
        $this->cliEchoUpgradeTable($issues_table);
        $issues_table->upgrade(upgrade_411\Issue::getB2DBTable());
        $projects_table = upgrade_416\Project::getB2DBTable();
        $this->cliEchoUpgradeTable($projects_table);
        $projects_table->upgrade(upgrade_411\Project::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.2';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot2()
    {
        $mail_queue_table = \thebuggenie\modules\mailing\entities\tables\MailQueueTable::getTable();
        $this->cliEchoUpgradeTable($mail_queue_table);
        $mail_queue_table->upgrade(upgrade_412\MailQueueTable::getTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.3';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot3()
    {
        $agile_board_table = \thebuggenie\modules\agile\entities\tables\AgileBoards::getTable();
        $this->cliEchoUpgradeTable($agile_board_table);
        $agile_board_table->upgrade(upgrade_413\AgileBoard::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.4';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot4()
    {
        $files_table = tables\Files::getTable();
        $this->cliEchoUpgradeTable($files_table);
        $files_table->upgrade(upgrade_414\File::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.5';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot5()
    {
        $issues_table = tables\Issues::getTable();
        $this->cliEchoUpgradeTable($issues_table);
        $issues_table->upgrade(upgrade_415\Issue::getB2DBTable());

        $issue_spent_time_table = tables\IssueSpentTimes::getTable();
        $this->cliEchoUpgradeTable($issue_spent_time_table);
        $issue_spent_time_table->upgrade(upgrade_415\IssueSpentTime::getB2DBTable());

        $issue_estimates_table = tables\IssueEstimates::getTable();
        $this->cliEchoUpgradeTable($issue_estimates_table);
        $issue_estimates_table->upgrade(upgrade_415\IssueEstimatesTable::getTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.6';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot6()
    {
        $projects_table = tables\Projects::getTable();
        $this->cliEchoUpgradeTable($projects_table);
        $projects_table->upgrade(upgrade_416\Project::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.7';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot7()
    {
        $notifications_table = tables\Notifications::getTable();
        $this->cliEchoUpgradeTable($notifications_table);
        $notifications_table->upgrade(upgrade_417\Notification::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.8';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot9()
    {
        $notification_settings_table = tables\NotificationSettings::getTable();
        $this->cliEchoUpgradeTable($notification_settings_table);
        $notification_settings_table->upgrade(upgrade_419\NotificationSetting::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.10';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot11()
    {
        $this->cliEchoAddIndexTable(tables\ScopeHostnames::getTable());
        tables\ScopeHostnames::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\Notifications::getTable());
        tables\Notifications::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\WorkflowTransitionValidationRules::getTable());
        tables\WorkflowTransitionValidationRules::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\WorkflowTransitionActions::getTable());
        tables\WorkflowTransitionActions::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\WorkflowStepTransitions::getTable());
        tables\WorkflowStepTransitions::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\Links::getTable());
        tables\Links::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\LogItems::getTable());
        tables\LogItems::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\Teams::getTable());
        tables\Teams::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\IssueCustomFields::getTable());
        tables\IssueCustomFields::getTable()->createIndexes();
        $this->cliEchoAddIndexTable(tables\ListTypes::getTable());
        tables\ListTypes::getTable()->createIndexes();

        $this->upgrade_complete = true;
        $this->current_version = '4.1.12';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot1dot12()
    {
        $comments_table = tables\Comments::getTable();
        $this->cliEchoUpgradedVersion($comments_table, true);
        $comments_table->upgrade(upgrade_4112\Comment::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.1.13';

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    /**
     * Gather information for the upgrade from versions <= 4.1.13
     *
     * @param framework\Request|null $request
     */
    protected function _prepareUpgradeFrom4dot1dot13(framework\Request $request = null)
    {
        $admin_username = upgrade_4112\UsersTable::getTable()->getAdminUsername();

        if (framework\Context::isCLI()) {
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
        $user_sessions_table = UserSessions::getTable();
        $this->cliEchoCreateTable($user_sessions_table);
        $user_sessions_table->create();

        if (framework\Context::isCLI()) {
            Command::cli_echo("Updating/fixing status of milestones.\n");
        }
        $milestones = tables\Milestones::getTable()->selectAll();
        foreach ($milestones as $milestone)
        {
            $milestone->updateStatus();
            $milestone->save();
        }

        if (framework\Context::isCLI()) {
            Command::cli_echo("Updating/fixing article types.\n");
        }
        Articles::getTable()->fixArticleTypes();

        if (framework\Context::isCLI()) {
            Command::cli_echo("Fixing file scopes.\n");
        }
        Files::getTable()->fixScopes();

        if (framework\Context::isCLI()) {
            Command::cli_echo("Fixing issue spent times scopes.\n");
        }
        IssueSpentTimes::getTable()->fixScopes();

        $scopes = Scope::getAll();
        $cc = 1;
        if (framework\Context::isCLI()) {
            Command::cli_echo("Implementing new workflows.\n");
        }
        $prev_percentage = 0;
        foreach ($scopes as $scope) {
            if (framework\Context::isCLI()) {
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

        $this->cliEchoUpgradedVersion($this->current_version);
    }

    protected function _upgradeFrom4dot2dot1()
    {
        $this->cliEchoCreateTable(tables\Branches::getTable());
        tables\Branches::getTable()->create();
        $this->cliEchoCreateTable(tables\Commits::getTable());
        tables\Commits::getTable()->create();
        $this->cliEchoCreateTable(tables\CommitFiles::getTable());
        tables\CommitFiles::getTable()->create();
        $this->cliEchoCreateTable(tables\BranchCommits::getTable());
        tables\BranchCommits::getTable()->create();
        $this->cliEchoCreateTable(tables\CommitFileDiffs::getTable());
        tables\CommitFileDiffs::getTable()->create();
        $this->cliEchoCreateTable(tables\IssueCommits::getTable());
        tables\IssueCommits::getTable()->create();
        $this->cliEchoCreateTable(tables\IssueFiles::getTable());
        tables\IssueFiles::getTable()->create();
        $this->cliEchoCreateTable(tables\LivelinkImports::getTable());
        tables\LivelinkImports::getTable()->create();

        $this->cliEchoUpgradeTable(tables\LogItems::getTable());
        tables\LogItems::getTable()->upgrade(upgrade_421\LogItem::getB2DBTable());
        $this->cliEchoUpgradeTable(IncomingEmailAccounts::getTable());
        IncomingEmailAccounts::getTable()->upgrade(upgrade_421\IncomingEmailAccount::getB2DBTable());

        $this->upgrade_complete = true;
        $this->current_version = '4.3.0';

        $this->cliEchoUpgradedVersion($this->current_version);
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
            if (framework\Context::isCLI()) {
                Command::cli_echo("Gathering information before upgrading...\n\n");
            }

            switch ($this->current_version) {
                case '4.2.1':
                case '4.2.0':
                    break;
                default:
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
                case '4.2.0':
                case '4.2.1':
                    $this->_upgradeFrom4dot2dot1();
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
