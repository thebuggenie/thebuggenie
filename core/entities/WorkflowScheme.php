<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;

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
            $scheme = new \thebuggenie\core\entities\WorkflowScheme();
            $scheme->setScope($scope);
            $scheme->setName("Default workflow scheme");
            $scheme->setDescription("This is the default workflow scheme. It is used by all projects with no specific workflow scheme selected. This scheme cannot be edited or removed.");
            $scheme->save();

            \thebuggenie\core\framework\Settings::saveSetting(\thebuggenie\core\framework\Settings::SETTING_DEFAULT_WORKFLOWSCHEME, $scheme->getID(), 'core', $scope->getID());
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

        /**
         * Whether this is the builtin workflow that cannot be
         * edited or removed
         *
         * @return boolean
         */
        public function isCore()
        {
            return ($this->getID() == \thebuggenie\core\framework\Settings::getCoreWorkflowScheme()->getID());
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
            else
            {
                return \thebuggenie\core\framework\Settings::getCoreWorkflow();
            }
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
