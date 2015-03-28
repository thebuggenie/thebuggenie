<?php

    namespace thebuggenie\core\helpers;

    use thebuggenie\core\framework,
        thebuggenie\core\entities;

    /**
     * actions for the project module
     */
    class ProjectActions extends framework\Action
    {
        /**
         * The currently selected project
         *
         * @var entities\Project
         * @access protected
         * @property $selected_project
         */

        /**
         * Pre-execute function
         *
         * @param framework\Request $request
         * @param string $action
         */
        public function preExecute(framework\Request $request, $action)
        {
            try
            {
                if ($project_id = $request['project_id'])
                    $this->selected_project = entities\Project::getB2DBTable()->selectById($project_id);
                elseif ($project_key = $request['project_key'])
                    $this->selected_project = entities\Project::getByKey($project_key);
            }
            catch (\Exception $e) { }

            if (!$this->selected_project instanceof entities\Project)
                return $this->return404(framework\Context::getI18n()->__('This project does not exist'));

            framework\Context::setCurrentProject($this->selected_project);
            $this->project_key = $this->selected_project->getKey();
        }

        protected function _checkProjectPageAccess($page)
        {
            return framework\Context::getUser()->hasProjectPageAccess($page, $this->selected_project);
        }

    }