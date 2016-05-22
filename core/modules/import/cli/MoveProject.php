<?php

    namespace thebuggenie\core\modules\import\cli;

    /**
     * CLI command class, import -> move_project
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */
    use b2db\Criteria;
    use b2db\Row;
    use thebuggenie\core\entities\Comment;
    use thebuggenie\core\entities\Datatype;
    use thebuggenie\core\entities\DatatypeBase;
    use thebuggenie\core\entities\Scope;
    use thebuggenie\core\entities\tables\Comments;
    use thebuggenie\core\entities\tables\CustomFields;
    use thebuggenie\core\entities\tables\Dashboards;
    use thebuggenie\core\entities\tables\DashboardViews;
    use thebuggenie\core\entities\tables\EditionComponents;
    use thebuggenie\core\entities\tables\Editions;
    use thebuggenie\core\entities\tables\Files;
    use thebuggenie\core\entities\tables\IssueAffectsBuild;
    use thebuggenie\core\entities\tables\IssueCustomFields;
    use thebuggenie\core\entities\tables\IssueEstimates;
    use thebuggenie\core\entities\tables\IssueFiles;
    use thebuggenie\core\entities\tables\IssueRelations;
    use thebuggenie\core\entities\tables\Issues;
    use thebuggenie\core\entities\tables\IssueSpentTimes;
    use thebuggenie\core\entities\tables\Links;
    use thebuggenie\core\entities\tables\ListTypes;
    use thebuggenie\core\entities\tables\Log;
    use thebuggenie\core\entities\tables\Projects;
    use thebuggenie\core\entities\tables\Scopes;
    use thebuggenie\core\entities\tables\UserIssues;
    use thebuggenie\core\entities\tables\Votes;

    /**
     * CLI command class, import -> move_project
     *
     * @package thebuggenie
     * @subpackage core
     */
    class MoveProject extends \thebuggenie\core\framework\cli\Command
    {

        protected function _getProjects()
        {
            $table = Projects::getTable();
            $crit = $table->getCriteria();
            $crit->addWhere('projects.scope', $this->getProvidedArgument('scope_id'));
            $crit->addOrderBy('projects.id', Criteria::SORT_ASC);
            $projects = $table->select($crit);

            return $projects;
        }

        protected function _getScope($hostname)
        {
            $row = Scopes::getTable()->getByHostname($hostname);
            if (!$row instanceof Row) {
                return null;
            }

            $scope = Scopes::getTable()->selectById($row['scopes.id']);
            return $scope;
        }

        protected function _setup()
        {
            $this->_command_name = 'move_project';
            $this->_description = "Move a project from one scope to another";
            $this->addRequiredArgument('scope_id', "The scope to read from");
            $this->addOptionalArgument('project_id', "The project to move");
            $this->addOptionalArgument('to_scope_id', "The scope to move the project to");
            $this->addOptionalArgument('skip-confirmation', "Whether to ask for confirmation before moving the project");
            $this->addOptionalArgument('verbose', "Whether to print extra information");
        }
        
        protected function updateCustomFields($issue_ids, $to_scope_id)
        {
            $this->cliEcho("Moving custom fields... ");
            $crit = IssueCustomFields::getTable()->getCriteria();
            $crit->addWhere('issuecustomfields.issue_id', $issue_ids, Criteria::DB_IN);
            $crit->addSelectionColumn('issuecustomfields.customfieldoption_id', 'option_id');
            $crit->addSelectionColumn('issuecustomfields.customfields_id', 'customfield_id');
            $customfields = [];
            $customfieldoptions = [];
            if ($res = IssueCustomFields::getTable()->doSelect($crit)) {
                while ($row = $res->getNextRow()) {
                    $customfields[] = $row['customfield_id'];
                }
            }

            $customfields_criteria = CustomFields::getTable()->getCriteria();
            $customfields_criteria->addWhere('customfields.scope', $to_scope_id);
            $customfields_criteria->indexBy('customfields.key');

            $to_scope_customfields = CustomFields::getTable()->select($customfields_criteria);

            $customfields_criteria = CustomFields::getTable()->getCriteria();
            $customfields_criteria->addWhere('customfields.scope', $this->from_scope);
            $customfields_criteria->indexBy('customfields.key');

            $from_scope_customfields = CustomFields::getTable()->select($customfields_criteria);

            foreach ($from_scope_customfields as $key => $customfield) {
                if (!array_key_exists($key, $to_scope_customfields)) {
                    $new_customfield = clone $customfield;
                    $new_customfield->setScope($to_scope_id);
                    $new_customfield->save();
                } else {
                    $new_customfield = $customfield;
                }
                $customfields[$customfield->getID()] = $new_customfield->getID();

                if ($customfield->hasCustomOptions()) {
                    $custom_options = $new_customfield->getOptions();

                    foreach ($customfield->getOptions() as $option) {
                        $found = false;
                        foreach ($custom_options as $custom_option) {
                            if ($custom_option->getValue() == $option->getValue()) {
                                $new_custom_option = $custom_option;
                                $found = true;
                                break;
                            }
                        }

                        if (!$found) {
                            $new_custom_option = clone $option;
                            $new_custom_option->setCustomdatatype($new_customfield);
                            $new_custom_option->setScope($to_scope_id);
                            $new_custom_option->save();
                        }

                        $customfieldoptions[$option->getID()] = $new_custom_option->getID();
                    }
                }
            }

            if (count($customfields)) {
                foreach ($customfields as $old_id => $new_id) {
                    $crit = IssueCustomFields::getTable()->getCriteria();
                    $crit->addWhere('issuecustomfields.customfields_id', $old_id);
                    $crit->addUpdate('issuecustomfields.customfields_id', $new_id);
                    $crit->addUpdate('issuecustomfields.scope', $to_scope_id);
                    IssueCustomFields::getTable()->doUpdate($crit);
                }
            }

            if (count($customfieldoptions)) {
                foreach ($customfieldoptions as $old_id => $new_id) {
                    $crit = IssueCustomFields::getTable()->getCriteria();
                    $crit->addWhere('issuecustomfields.customfieldoption_id', $old_id);
                    $crit->addUpdate('issuecustomfields.customfieldoption_id', $new_id);
                    $crit->addUpdate('issuecustomfields.scope', $to_scope_id);
                    IssueCustomFields::getTable()->doUpdate($crit);
                }
            }
            $this->cliEcho(" done\n");
        }

        protected function moveIssues($project_id, $to_scope_id)
        {
            $crit = Issues::getTable()->getCriteria();
            $crit->addWhere('issues.project_id', $project_id);
            $crit->addSelectionColumn('issues.id', 'id');

            $issue_ids = [];
            if ($res = Issues::getTable()->doSelect($crit)) {
                while ($row = $res->getNextRow()) {
                    $issue_ids[] = $row['id'];
                }
            }

            $this->cliEcho("------------------\n");
            $this->cliEcho('Moving ');
            $this->cliEcho(count($issue_ids), 'white', 'bold');
            $this->cliEcho(" issues\n");
            $this->cliEcho("------------------\n");

            if (!$issue_ids) return;

            $this->cliEcho("Moving comments... ");
            $comments_crit = Comments::getTable()->getCriteria();
            $comments_crit->addUpdate('comments.scope', $to_scope_id);
            $comments_crit->addWhere('comments.target_id', $issue_ids, Criteria::DB_IN);
            $comments_crit->addWhere('comments.target_type', Comment::TYPE_ISSUE);
            Comments::getTable()->doUpdate($comments_crit);
            $this->cliEcho(" done\n");

            $this->cliEcho("Moving log items... ");
            $logs_crit = Log::getTable()->getCriteria();
            $logs_crit->addUpdate('log.scope', $to_scope_id);
            $logs_crit->addWhere('log.target', $issue_ids, Criteria::DB_IN);
            $logs_crit->addWhere('log.target_type', Log::TYPE_ISSUE);
            Log::getTable()->doUpdate($logs_crit);
            $this->cliEcho(" done\n");

            $this->cliEcho("Moving attachments... ");
            $file_ids_crit = IssueFiles::getTable()->getCriteria();
            $file_ids_crit->addWhere('issuefiles.issue_id', $issue_ids, Criteria::DB_IN);
            $file_ids_crit->addSelectionColumn('issuefiles.file_id', 'file_id');
            $file_ids_crit->addSelectionColumn('issuefiles.id', 'id');
            $file_ids = [];
            $issue_file_ids = [];
            if ($res = IssueFiles::getTable()->doSelect($file_ids_crit)) {
                while ($row = $res->getNextRow()) {
                    $file_ids[] = $row['file_id'];
                    $issue_file_ids[] = $row['id'];
                }
            }

            if (count($file_ids)) {
                $file_crit = Files::getTable()->getCriteria();
                $file_crit->addUpdate('files.scope', $to_scope_id);
                $file_crit->addWhere('files.id', $file_ids, Criteria::DB_IN);
                Files::getTable()->doUpdate($file_crit);

                $issue_file_crit = IssueFiles::getTable()->getCriteria();
                $issue_file_crit->addUpdate('issuefiles.scope', $to_scope_id);
                $issue_file_crit->addWhere('issuefiles.id', $issue_file_ids, Criteria::DB_IN);
                IssueFiles::getTable()->doUpdate($issue_file_crit);
            }
            $this->cliEcho(" done\n");

            $this->cliEcho("Moving calculations and estimations... ");
            $estimates_crit = IssueEstimates::getTable()->getCriteria();
            $estimates_crit->addUpdate('issue_estimates.scope', $to_scope_id);
            $estimates_crit->addWhere('issue_estimates.issue_id', $issue_ids, Criteria::DB_IN);
            IssueEstimates::getTable()->doUpdate($estimates_crit);

            $spent_crit = IssueSpentTimes::getTable()->getCriteria();
            $spent_crit->addUpdate('issue_spenttimes.scope', $to_scope_id);
            $spent_crit->addWhere('issue_spenttimes.issue_id', $issue_ids, Criteria::DB_IN);
            IssueSpentTimes::getTable()->doUpdate($spent_crit);
            $this->cliEcho(" done\n");

            $this->cliEcho("Moving links, related and affected items");
            $tables = [
                '\thebuggenie\core\entities\tables\IssueAffectsBuild' => 'issueaffectsbuild',
                '\thebuggenie\core\entities\tables\IssueAffectsComponent' => 'issueaffectscomponent',
                '\thebuggenie\core\entities\tables\IssueAffectsEdition' => 'issueaffectsedition',
            ];
            foreach ($tables as $class_name => $table_name) {
                $crit = $class_name::getTable()->getCriteria();
                $crit->addUpdate($table_name.'.scope', $to_scope_id);
                $crit->addWhere($table_name.'.issue', $issue_ids, Criteria::DB_IN);
                $class_name::getTable()->doUpdate($crit);
            }

            $links_crit = Links::getTable()->getCriteria();
            $links_crit->addUpdate('links.scope', $to_scope_id);
            $links_crit->addWhere('links.target_id', $issue_ids, Criteria::DB_IN);
            $links_crit->addWhere('links.target_type', 'issue');
            Links::getTable()->doUpdate($links_crit);

            $related_crit = IssueRelations::getTable()->getCriteria();
            $related_crit->addUpdate('issuerelations.scope', $to_scope_id);
            $ctn = $related_crit->returnCriterion('issuerelations.child_id', $issue_ids, Criteria::DB_IN);
            $ctn->addOr('issuerelations.parent_id', $issue_ids, Criteria::DB_IN);
            $related_crit->addWhere($ctn);

            $votes_crit = Votes::getTable()->getCriteria();
            $votes_crit->addUpdate('votes.scope', $to_scope_id);
            $votes_crit->addWhere('votes.target', $issue_ids, Criteria::DB_IN);
            Votes::getTable()->doUpdate($votes_crit);
            $this->cliEcho(" done\n");

            $this->cliEcho("Updating user issue bookmarks");
            $user_issues_crit = UserIssues::getTable()->getCriteria();
            $user_issues_crit->addUpdate('userissues.scope', $to_scope_id);
            $user_issues_crit->addWhere('userissues.issue', $issue_ids, Criteria::DB_IN);
            UserIssues::getTable()->doUpdate($user_issues_crit);
            $this->cliEcho(" done\n");

            $this->updateCustomFields($issue_ids, $to_scope_id);

            $crit = Issues::getTable()->getCriteria();
            $crit->addUpdate('issues.scope', $to_scope_id);
            $crit->addWhere('issues.id', $issue_ids, Criteria::DB_IN);
            Issues::getTable()->doUpdate($crit);

            $this->cliEcho("------------------\n");
            $this->cliEcho("Done moving issues\n");
            $this->cliEcho("------------------\n");
        }

        protected function moveDatatypes($project_id, $to_scope_id)
        {
            $this->cliEcho("Moving datatypes...");
            $datatype_criteria = ListTypes::getTable()->getCriteria();
            $datatype_criteria->addWhere('listtypes.scope', $to_scope_id);

            $to_scope_datatypes = [];
            $listtypes = ListTypes::getTable()->select($datatype_criteria);
            foreach ($listtypes as $listtype) {
                if (!array_key_exists($listtype->getItemtype(), $to_scope_datatypes)) {
                    $to_scope_datatypes[$listtype->getItemtype()] = [];
                }
                $to_scope_datatypes[$listtype->getItemtype()][$listtype->getKey()] = $listtype;
            }

            $datatype_criteria = ListTypes::getTable()->getCriteria();
            $datatype_criteria->addWhere('listtypes.scope', $this->from_scope);

            $todo_datatypes = [];
            $listtypes = ListTypes::getTable()->select($datatype_criteria);
            foreach ($listtypes as $listtype) {
                if (!array_key_exists($listtype->getItemtype(), $todo_datatypes)) {
                    $todo_datatypes[$listtype->getItemtype()] = [];
                }
                if (array_key_exists($listtype->getKey(), $to_scope_datatypes[$listtype->getItemtype()])) {
                    $todo_datatypes[$listtype->getItemtype()][$listtype->getId()] = $to_scope_datatypes[$listtype->getItemtype()][$listtype->getKey()]->getId();
                } else {
                    $new_listtype = clone $listtype;
                    $new_listtype->setScope($to_scope_id);
                    $new_listtype->save();

                    $todo_datatypes[$listtype->getItemtype()][$listtype->getId()] = $new_listtype->getId();
                }
            }

            foreach ($todo_datatypes as $itemtype => $types) {
                switch ($itemtype) {
                    case Datatype::STATUS:
                        $field = 'issue.status';
                        break;
                    case Datatype::CATEGORY:
                        $field = 'issue.category';
                        break;
                    case Datatype::ISSUETYPE:
                        $field = 'issue.issuetype';
                        break;
                    case Datatype::PRIORITY:
                        $field = 'issue.priority';
                        break;
                    case Datatype::REPRODUCABILITY:
                        $field = 'issue.reproducability';
                        break;
                    case Datatype::RESOLUTION:
                        $field = 'issue.resolution';
                        break;
                    case Datatype::SEVERITY:
                        $field = 'issue.severity';
                        break;
                    default:
                        $field = false;
                }
                if ($field !== false) {
                    foreach ($types as $current_type_id => $new_type_id) {
                        $crit = Issues::getTable()->getCriteria();
                        $crit->addWhere('issues.project_id', $project_id);
                        $crit->addWhere($field, $current_type_id);
                        $crit->addUpdate($field, $new_type_id);

                        Issues::getTable()->doUpdate($crit);
                    }
                }
            }
            $this->cliEcho(" done\n");
        }

        protected function moveProject($project_id, $to_scope_id)
        {
            $this->cliEcho("--------------\n");
            $this->cliEcho("Moving project\n");
            $this->cliEcho("--------------\n");

            $this->moveIssues($project_id, $to_scope_id);
            $this->cliEcho("Moving components, editions and releases...");
            $tables = [
                '\thebuggenie\core\entities\tables\Components' => 'components',
                '\thebuggenie\core\entities\tables\Editions' => 'editions',
                '\thebuggenie\core\entities\tables\Builds' => 'builds',
            ];
            foreach ($tables as $class_name => $table_name) {
                $crit = $class_name::getTable()->getCriteria();
                $crit->addUpdate($table_name.'.scope', $to_scope_id);
                $crit->addWhere($table_name.'.project', $project_id);
                $class_name::getTable()->doUpdate($crit);
            }

            $edition_criteria = Editions::getTable()->getCriteria();
            $edition_criteria->addWhere('editions.project', $project_id);
            $edition_criteria->addSelectionColumn('editions.id', 'id');
            if ($res = Editions::getTable()->doSelect($edition_criteria)) {
                while ($row = $res->getNextRow()) {
                    $edition_id = $row['id'];
                    $edition_criteria = EditionComponents::getTable()->getCriteria();
                    $edition_criteria->addWhere('editioncomponents.edition', $edition_id);
                    $edition_criteria->addUpdate('editioncomponents.scope', $to_scope_id);
                    EditionComponents::getTable()->doUpdate($edition_criteria);
                }
            }
            $this->cliEcho(" done\n");
            $this->cliEcho("Moving project dashboard...");
            $dashboard_criteria = Dashboards::getTable()->getCriteria();
            $dashboard_criteria->addWhere('dashboards.project_id', $project_id);
            $dashboard_criteria->addSelectionColumn('dashboards.id', 'id');
            $dashboard_ids = [];
            if ($res = Dashboards::getTable()->doSelect($dashboard_criteria)) {
                while ($row = $res->getNextRow()) {
                    $dashboard_ids[] = $row['id'];
                }

                $dashboardviews_criteria = DashboardViews::getTable()->getCriteria();
                $dashboardviews_criteria->addWhere('dashboard_views.dashboard_id', $dashboard_ids, Criteria::DB_IN);
                $dashboardviews_criteria->addUpdate('dashboards.scope', $to_scope_id);
                DashboardViews::getTable()->doUpdate($dashboardviews_criteria);

                $dashboard_criteria = Dashboards::getTable()->getCriteria();
                $dashboard_criteria->addWhere('dashboards.project_id', $project_id);
                $dashboard_criteria->addUpdate('dashboards.scope', $to_scope_id);
                Dashboards::getTable()->doUpdate($dashboard_criteria);
            }
            $this->cliEcho(" done\n");

            $this->moveDatatypes($project_id, $to_scope_id);

            $crit = Projects::getTable()->getCriteria();
            $crit->addUpdate('projects.scope', $to_scope_id);
            Projects::getTable()->doUpdateById($crit, $project_id);

            $this->cliEcho("-------------------\n");
            $this->cliEcho("Done moving project\n");
            $this->cliEcho("-------------------\n");
        }

        public function do_execute()
        {
            $from_scope = Scopes::getTable()->selectById($this->getProvidedArgument('scope_id'));
            $verbose = (bool) $this->getProvidedArgument('verbose');

            if (!$from_scope instanceof Scope) {
                throw new \Exception("Cannot read from scope ".$this->getProvidedArgument('scope_id'));
            }

            $this->cliEcho("Reading project list from scope {$from_scope->getID()} (".join($from_scope->getHostnames(),',').")\n");
            $this->from_scope = $from_scope->getID();

            $projects = $this->_getProjects();

            if (!count($projects)) {
                throw new \Exception("There are no projects to move");
            }

            $project_id = $this->getProvidedArgument('project_id');
            if (!$project_id) {
                $this->cliEcho("\n");
                $this->cliEcho("Please choose the project to move from the list below:\n");
                foreach ($projects as $project_id => $project) {
                    $this->cliEcho($project->getID().': ', 'white', 'bold');
                    $this->cliEcho('['.$project->getKey().'] '.$project->getName()."\n");
                }
                $this->cliEcho("\n");
                $this->cliEcho("Enter the ID of the project you want to move, or 'all' to move all projects: ");
                $project_id = (int) $this->getInput();
            }

            if (strpos($project_id, ',') !== false) {
                $project_ids = explode(',', $project_id);
            } elseif ($project_id == 'all') {
                $project_ids = array_keys($projects);
            } else {
                $project_ids = [$project_id];
            }

            foreach ($project_ids as $project_id) {

                if (!array_key_exists($project_id, $projects)) {
                    var_dump($project_id);
                    throw new \Exception("Please select a project id from the list");
                }
                $project = $projects[$project_id];

                $to_scope_id = $this->getProvidedArgument('to_scope_id');
                if (!$to_scope_id) {
                    $this->cliEcho("\n");
                    $this->cliEcho("Enter the hostname of the scope you want to move this project to, or press [Enter] for the default scope.\n");
                    $this->cliEcho("Hostname [default]: ");
                    $hostname = $this->getInput();

                    $to_scope = $this->_getScope($hostname);
                } else {
                    $to_scope = Scopes::getTable()->selectById($to_scope_id);
                }

                if (!$to_scope instanceof Scope) {
                    throw new \Exception("Could not find target scope");
                }

                $to_scope_id = $to_scope->getID();

                if ($to_scope_id == $from_scope->getID()) {
                    throw new \Exception("Cannot move the project to the same scope");
                }

                $this->cliEcho("\n");
                $this->cliEcho("Moving project ", 'green');
                if ($verbose) {
                    $this->cliEcho($project->getName()." [{$project->getID()}]", 'white', 'bold');
                } else {
                    $this->cliEcho($project->getName(), 'white', 'bold');
                }
                if ($from_scope->isDefault()) {
                    $this->cliEcho(" from ", 'green');
                    $this->cliEcho("default scope", 'white', 'bold');
                } else {
                    $this->cliEcho(" from ", 'green');
                    if ($verbose) {
                        $this->cliEcho(join($from_scope->getHostnames(), ',')." [{$from_scope->getID()}]", 'white', 'bold');
                    } else {
                        $this->cliEcho(join($from_scope->getHostnames(), ','), 'white', 'bold');
                    }
                }
                $this->cliEcho(" to ", 'green');
                if ($verbose) {
                    $this->cliEcho(join($to_scope->getHostnames(), ',')." [{$to_scope->getID()}]", 'white', 'bold');
                } else {
                    $this->cliEcho(join($to_scope->getHostnames(), ','), 'white', 'bold');
                }
                $this->cliEcho("\n");
                $this->cliEcho("\n");

                if ($this->getProvidedArgument('skip-confirmation', 'no') != 'yes') {
                    $this->cliEcho("Please type [yes] to start moving the project: \n");

                    if (!$this->getInputConfirmation()) {
                        $this->cliEcho("Cancelled", 'red', 'bold');
                        $this->cliEcho("\n");
                        $this->cliEcho("\n");
                        return;
                    }
                }

                $this->moveProject($project_id, $to_scope_id);
            }
        }

    }
