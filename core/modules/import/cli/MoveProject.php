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
    use thebuggenie\core\entities\Scope;
    use thebuggenie\core\entities\tables\Projects;
    use thebuggenie\core\entities\tables\Scopes;

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
            $this->addOptionalArgument('verbose', "Whether to print extra information");
        }

        public function do_execute()
        {
            $from_scope = Scopes::getTable()->selectById($this->getProvidedArgument('scope_id'));
            $verbose = (bool) $this->getProvidedArgument('verbose');

            if (!$from_scope instanceof Scope) {
                throw new \Exception("Cannot read from scope ".$this->getProvidedArgument('scope_id'));
            }

            $this->cliEcho("Reading project list from scope {$from_scope->getID()} (".join($from_scope->getHostnames(),',').")\n");
            $projects = $this->_getProjects();
            $project_id = $this->getProvidedArgument('project_id');
            if (!$project_id) {
                $this->cliEcho("\n");
                $this->cliEcho("Please choose the project to move from the list below:\n");
                foreach ($projects as $project_id => $project) {
                    $this->cliEcho($project->getID().': ', 'white', 'bold');
                    $this->cliEcho('['.$project->getKey().'] '.$project->getName()."\n");
                }
                $this->cliEcho("\n");
                $this->cliEcho("Enter the ID of the project you want to move: ");
                $project_id = (int) $this->getInput();
            }

            if (!array_key_exists($project_id, $projects)) {
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
            $this->cliEcho("Please type [yes] to start moving the project: \n");

            if (!$this->getInputConfirmation()) {
                $this->cliEcho("Cancelled", 'red', 'bold');
                $this->cliEcho("\n");
                $this->cliEcho("\n");
                return;
            }

            $this->moveProject($project_id, $to_scope_id);
        }

    }
