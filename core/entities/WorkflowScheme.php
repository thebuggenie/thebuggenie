<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework\Settings;

    /**
     * Workflow scheme class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Workflow scheme class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\WorkflowSchemes")
     */
    class WorkflowScheme extends IdentifiableScoped
    {

        protected static $_schemes = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        protected $_issuetype_workflows = null;

        protected $_num_issuetype_workflows = null;

        /**
         * Projects using this workflow scheme
         *
         * @var array|\thebuggenie\core\entities\Project
         * @Relates(class="\thebuggenie\core\entities\Project", collection=true, foreign_column="workflow_scheme_id")
         */
        protected $_projects = null;

        /**
         * The workflow description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = null;

        protected static function _populateSchemes()
        {
            if (self::$_schemes === null)
            {
                self::$_schemes = tables\WorkflowSchemes::getTable()->getAll();
            }
        }
        
        /**
         * Return all workflow schemes in the system
         *
         * @return array An array of \thebuggenie\core\entities\WorkflowScheme objects
         */
        public static function getAll()
        {
            self::_populateSchemes();
            return self::$_schemes;
        }
        
        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $multi_team_workflow_scheme = new WorkflowScheme();
            $multi_team_workflow_scheme->setScope($scope);
            $multi_team_workflow_scheme->setName("Multi-team workflow scheme");
            $multi_team_workflow_scheme->setDescription("This is a workflow scheme well suited for projects with multiple teams. It uses the multi-team workflow for all issue types.");
            $multi_team_workflow_scheme->save();
            Settings::saveSetting(Settings::SETTING_MULTI_TEAM_WORKFLOW_SCHEME, $multi_team_workflow_scheme->getID(), 'core', $scope->getID());

            $balanced_workflow_scheme = new WorkflowScheme();
            $balanced_workflow_scheme->setScope($scope);
            $balanced_workflow_scheme->setName("Balanced workflow scheme");
            $balanced_workflow_scheme->setDescription("This is a workflow scheme used to handle medium-sized projects or small-team projects. It uses the balanced workflow for all issue types.");
            $balanced_workflow_scheme->save();
            Settings::saveSetting(Settings::SETTING_BALANCED_WORKFLOW_SCHEME, $balanced_workflow_scheme->getID(), 'core', $scope->getID());

            $simple_workflow_scheme = new WorkflowScheme();
            $simple_workflow_scheme->setScope($scope);
            $simple_workflow_scheme->setName("Simple workflow scheme");
            $simple_workflow_scheme->setDescription("This is a simple workflow scheme for projects with few people, or even just one person. It uses the simple workflow for all issue types.");
            $simple_workflow_scheme->save();
            Settings::saveSetting(Settings::SETTING_SIMPLE_WORKFLOW_SCHEME, $simple_workflow_scheme->getID(), 'core', $scope->getID());

            return [$multi_team_workflow_scheme, $balanced_workflow_scheme, $simple_workflow_scheme];
        }

        protected function _preDelete()
        {
            tables\WorkflowIssuetype::getTable()->deleteByWorkflowSchemeID($this->getID());
        }

        /**
         * Returns the workflows description
         *
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }
        
        /**
         * Set the workflows description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        protected function _populateAssociatedWorkflows()
        {
            if ($this->_issuetype_workflows === null)
            {
                $this->_issuetype_workflows = tables\WorkflowIssuetype::getTable()->getByWorkflowSchemeID($this->getID());
            }
        }

        public function getNumberOfAssociatedWorkflows()
        {
            if ($this->_num_issuetype_workflows === null && $this->_issuetype_workflows !== null)
            {
                $this->_num_issuetype_workflows = count($this->_issuetype_workflows);
            }
            elseif ($this->_num_issuetype_workflows === null)
            {
                $this->_num_issuetype_workflows = tables\WorkflowIssuetype::getTable()->countByWorkflowSchemeID($this->getID());
            }
            return $this->_num_issuetype_workflows;
        }

        public function hasWorkflowAssociatedWithIssuetype(\thebuggenie\core\entities\Issuetype $issuetype)
        {
            $this->_populateAssociatedWorkflows();
            return array_key_exists($issuetype->getID(), $this->_issuetype_workflows);
        }
        
        public function associateIssuetypeWithWorkflow(\thebuggenie\core\entities\Issuetype $issuetype, \thebuggenie\core\entities\Workflow $workflow)
        {
            tables\WorkflowIssuetype::getTable()->setWorkflowIDforIssuetypeIDwithSchemeID($workflow->getID(), $issuetype->getID(), $this->getID());
        }
        public function unassociateIssuetype(\thebuggenie\core\entities\Issuetype $issuetype)
        {
            tables\WorkflowIssuetype::getTable()->setWorkflowIDforIssuetypeIDwithSchemeID(null, $issuetype->getID(), $this->getID());
        }

        /**
         * Get the workflow associated with this issue type
         *
         * @return Workflow The associated workflow for this issue type
         */
        public function getWorkflowForIssuetype(\thebuggenie\core\entities\Issuetype $issuetype)
        {
            $this->_populateAssociatedWorkflows();
            if (array_key_exists($issuetype->getID(), $this->_issuetype_workflows))
            {
                return $this->_issuetype_workflows[$issuetype->getID()];
            }

            throw new \Exception('This issue type is missing workflow settings');
        }

        public function isInUse()
        {
            return (bool) $this->getNumberOfProjects();
        }
        
        public function getNumberOfProjects()
        {
            if ($this->_projects === null)
            {
                return $this->_b2dbLazycount('_projects');
            }
            return count($this->_projects);
        }
        
        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

    }
