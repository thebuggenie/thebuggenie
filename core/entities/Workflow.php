<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * Workflow class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Workflow class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Workflows")
     */
    class Workflow extends IdentifiableScoped
    {

        protected static $_workflows = null;
        
        protected static $_num_workflows = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The workflow description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = null;

        /**
         * Whether the workflow is active or not
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_is_active = true;

        /**
         * This workflow's steps
         *
         * @var array|\thebuggenie\core\entities\WorkflowStep
         * @Relates(class="\thebuggenie\core\entities\WorkflowStep", collection=true, foreign_column="workflow_id")
         */
        protected $_steps = null;

        protected $_num_steps = null;

        /**
         * The initial transition for incoming issues in this workflow
         *
         * @var \thebuggenie\core\entities\WorkflowTransition
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\WorkflowTransition")
         */
        protected $_initial_transition_id = null;

        /**
         * This workflow's transitions
         *
         * @var array|\thebuggenie\core\entities\WorkflowTransition
         * @Relates(class="\thebuggenie\core\entities\WorkflowTransition", collection=true, foreign_column="workflow_id")
         */
        protected $_transitions = null;
        
        /**
         * This workflow's schemes
         *
         * @var array|\thebuggenie\core\entities\WorkflowTransition
         * @Relates(class="\thebuggenie\core\entities\WorkflowScheme", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\WorkflowIssuetype")
         */
        protected $_schemes = null;

        protected $_num_schemes = null;
        
        protected static function _populateWorkflows()
        {
            if (self::$_workflows === null)
            {
                self::$_workflows = tables\Workflows::getTable()->getAll();
            }
        }
        
        /**
         * Return all workflows in the system
         *
         * @return array An array of \thebuggenie\core\entities\Workflow objects
         */
        public static function getAll()
        {
            self::_populateWorkflows();
            return self::$_workflows;
        }
        
        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $workflow = new \thebuggenie\core\entities\Workflow();
            $workflow->setName("Default workflow");
            $workflow->setDescription("This is the default workflow. It is used by all projects with no specific workflow selected, and for issue types with no specific workflow specified. This workflow cannot be edited or removed.");
            $workflow->setScope($scope->getID());
            $workflow->save();

            \thebuggenie\core\framework\Settings::saveSetting(\thebuggenie\core\framework\Settings::SETTING_DEFAULT_WORKFLOW, $workflow->getID(), 'core', $scope->getID());
            WorkflowStep::loadFixtures($scope, $workflow);
        }

        public static function getWorkflowsCount()
        {
            if (self::$_num_workflows === null)
            {
                if (self::$_workflows !== null)
                    self::$_num_workflows = count(self::$_workflows);
                else
                    self::$_num_workflows = tables\Workflows::getTable()->countWorkflows();
            }

            return self::$_num_workflows;
        }

        public static function getCustomWorkflowsCount()
        {
            return self::getWorkflowsCount() - 1;
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
            return ($this->getID() == \thebuggenie\core\framework\Settings::getCoreWorkflow()->getID());
        }

        /**
         * Whether this is the builtin workflow that cannot be edited or removed
         *
         * @return boolean
         */
        public function isActive()
        {
            return (bool) $this->_is_active;
        }

        protected function _populateTransitions()
        {
            if ($this->_transitions === null)
            {
                $this->_b2dbLazyload('_transitions');
                if (array_key_exists($this->getInitialTransition()->getID(), $this->_transitions)) unset($this->_transitions[$this->getInitialTransition()->getID()]);
            }
        }
        
        /**
         * Get all transitions in this workflow
         *
         * @return array An array of \thebuggenie\core\entities\WorkflowTransition objects
         */
        public function getTransitions()
        {
            $this->_populateTransitions();
            return $this->_transitions;
        }

        protected function _populateSteps()
        {
            if ($this->_steps === null)
            {
                $this->_b2dbLazyload('_steps');
            }
        }

        /**
         * Get all steps in this workflow
         *
         * @return array An array of \thebuggenie\core\entities\WorkflowStep objects
         */
        public function getSteps()
        {
            $this->_populateSteps();
            return $this->_steps;
        }

        /**
         * Get the first step in this workflow
         *
         * @return \thebuggenie\core\entities\WorkflowStep
         */
        public function getFirstStep()
        {
            return $this->getInitialTransition()->getOutgoingStep();
        }

        public function getNumberOfSteps()
        {
            if ($this->_num_steps === null && $this->_steps !== null)
            {
                $this->_num_steps = count($this->_steps);
            }
            elseif ($this->_num_steps === null)
            {
                $this->_num_steps = $this->_b2dbLazycount('_steps');
            }
            return (int) $this->_num_steps;
        }

        public function isInUse()
        {
            return (bool) $this->getNumberOfSchemes();
        }
        
        public function getNumberOfSchemes()
        {
            if ($this->_num_schemes === null && $this->_schemes !== null)
            {
                $this->_num_schemes = count($this->_schemes);
            }
            elseif ($this->_num_schemes === null)
            {
                $this->_num_schemes = $this->_b2dbLazycount('_schemes');
            }
            return $this->_num_schemes;
        }
        
        public function copy($new_name)
        {
            $new_workflow = new \thebuggenie\core\entities\Workflow();
            $new_workflow->setName($new_name);
            $new_workflow->save();
            $step_mapper = array();
            $transition_mapper = array();
            foreach ($this->getSteps() as $key => $step)
            {
                $this->_steps[$key] = $step->copy($new_workflow);
                $step_mapper[$key] = $this->_steps[$key]->getID();
            }
            foreach ($this->getTransitions() as $key => $transition)
            {
                $old_id = $transition->getID();
                $this->_transitions[$key] = $transition->copy($new_workflow);
                $transition_mapper[$old_id] = $this->_transitions[$key]->getID();
            }
            tables\WorkflowStepTransitions::getTable()->copyByWorkflowIDs($this->getID(), $new_workflow->getID());
            tables\WorkflowStepTransitions::getTable()->reMapStepIDsByWorkflowID($new_workflow->getID(), $step_mapper);
            tables\WorkflowTransitions::getTable()->reMapByWorkflowID($new_workflow->getID(), $step_mapper);
            tables\WorkflowStepTransitions::getTable()->reMapTransitionIDsByWorkflowID($new_workflow->getID(), $transition_mapper);

            $new_initial_transition = $this->getInitialTransition()->copy($new_workflow);
            $new_initial_transition->setOutgoingStepID($step_mapper[$this->getInitialTransition()->getOutgoingStep()->getID()]);
            $new_initial_transition->save();
            $new_workflow->setInitialTransition($new_initial_transition);
            $new_workflow->save();
            
            return $new_workflow;
        }

        public function moveIssueToMatchingWorkflowStep(\thebuggenie\core\entities\Issue $issue)
        {
            $change_step = false;
            
            if ($issue->isStatusChanged() || $issue->isResolutionChanged())
            {
                $change_step = true;
            }
            
            if ($change_step)
            {
                foreach ($this->getSteps() as $step)
                {
                    if ($step->hasLinkedStatus() && $issue->getStatus() instanceof \thebuggenie\core\entities\Status && $step->getLinkedStatusID() == $issue->getStatus()->getID())
                    {
                        $step->applyToIssue($issue);
                        return true;
                    }
                }
                foreach ($this->getSteps() as $step)
                {
                    if (!$step->hasLinkedStatus())
                    {
                        foreach ($step->getIncomingTransitions() as $transition)
                        {
                            if ($transition->hasPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID))
                            {
                                $rule = $transition->getPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID);
                                if ($rule->isValid($issue))
                                {
                                    $step->applyToIssue($issue);
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
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

        /**
         * Return the workflow's initial transition
         *
         * @return \thebuggenie\core\entities\WorkflowTransition
         */
        public function getInitialTransition()
        {
            return $this->_b2dbLazyload('_initial_transition_id');
        }

        public function setInitialTransition(\thebuggenie\core\entities\WorkflowTransition $transition)
        {
            $this->_initial_transition_id = $transition;
        }

    }
