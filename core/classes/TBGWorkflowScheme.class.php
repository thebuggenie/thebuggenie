<?php

	/**
	 * Workflow scheme class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow scheme class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 *
	 * @Table(name="TBGWorkflowSchemesTable")
	 */
	class TBGWorkflowScheme extends TBGIdentifiableScopedClass
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
		 * @var array|TBGProject
		 * @Relates(class="TBGProject", collection=true, foreign_column="workflow_scheme_id")
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
				self::$_schemes = TBGWorkflowSchemesTable::getTable()->getAll();
			}
		}
		
		/**
		 * Return all workflow schemes in the system
		 *
		 * @return array An array of TBGWorkflowScheme objects
		 */
		public static function getAll()
		{
			self::_populateSchemes();
			return self::$_schemes;
		}
		
		public static function loadFixtures(TBGScope $scope)
		{
			$scheme = new TBGWorkflowScheme();
			$scheme->setScope($scope);
			$scheme->setName("Default workflow scheme");
			$scheme->setDescription("This is the default workflow scheme. It is used by all projects with no specific workflow scheme selected. This scheme cannot be edited or removed.");
			$scheme->save();

			TBGSettings::saveSetting(TBGSettings::SETTING_DEFAULT_WORKFLOWSCHEME, $scheme->getID());
		}

		protected function _preDelete()
		{
			TBGWorkflowIssuetypeTable::getTable()->deleteByWorkflowSchemeID($this->getID());
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
			return ($this->getID() == TBGSettings::getCoreWorkflowScheme()->getID());
		}

		protected function _populateAssociatedWorkflows()
		{
			if ($this->_issuetype_workflows === null)
			{
				$this->_issuetype_workflows = TBGWorkflowIssuetypeTable::getTable()->getByWorkflowSchemeID($this->getID());
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
				$this->_num_issuetype_workflows = TBGWorkflowIssuetypeTable::getTable()->countByWorkflowSchemeID($this->getID());
			}
			return $this->_num_issuetype_workflows;
		}

		public function hasWorkflowAssociatedWithIssuetype(TBGIssuetype $issuetype)
		{
			$this->_populateAssociatedWorkflows();
			return array_key_exists($issuetype->getID(), $this->_issuetype_workflows);
		}
		
		public function associateIssuetypeWithWorkflow(TBGIssuetype $issuetype, TBGWorkflow $workflow)
		{
			TBGWorkflowIssuetypeTable::getTable()->setWorkflowIDforIssuetypeIDwithSchemeID($workflow->getID(), $issuetype->getID(), $this->getID());
		}
		public function unassociateIssuetype(TBGIssuetype $issuetype)
		{
			TBGWorkflowIssuetypeTable::getTable()->setWorkflowIDforIssuetypeIDwithSchemeID(null, $issuetype->getID(), $this->getID());
		}

		/**
		 * Get all steps in this workflow
		 *
		 * @return array An array of TBGWorkflowStep objects
		 */
		public function getWorkflowForIssuetype(TBGIssuetype $issuetype)
		{
			$this->_populateAssociatedWorkflows();
			if (array_key_exists($issuetype->getID(), $this->_issuetype_workflows))
			{
				return $this->_issuetype_workflows[$issuetype->getID()];
			}
			else
			{
				return TBGSettings::getCoreWorkflow();
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
				$this->_b2dbLazycount('_projects');
			}
			return $this->_projects;
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
