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
	 */
	class TBGWorkflowScheme extends TBGIdentifiableClass
	{

		static protected $_b2dbtablename = 'TBGWorkflowSchemesTable';
		
		protected static $_schemes = null;

		protected $_issuetype_workflows = null;

		protected $_num_issuetype_workflows = null;
		
		protected $_number_of_projects = null;

		/**
		 * The workflow description
		 *
		 * @var string
		 */
		protected $_description = null;

		/**
		 * Return all workflows in the system
		 *
		 * @return array An array of TBGWorkflow objects
		 */
		public static function getAll()
		{
			if (self::$_schemes === null)
			{
				self::$_schemes = array();
				if ($res = TBGWorkflowSchemesTable::getTable()->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_schemes[$row->get(TBGWorkflowSchemesTable::ID)] = TBGContext::factory()->TBGWorkflowScheme($row->get(TBGWorkflowSchemesTable::ID), $row);
					}
				}
			}
			return self::$_schemes;
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
			return ($this->getID() == 1);
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
				return TBGContext::factory()->TBGWorkflow(1);
			}
		}

		public function isInUse()
		{
			if ($this->_number_of_projects === null)
			{
				$this->_number_of_projects = TBGProjectsTable::getTable()->countByWorkflowSchemeID($this->getID());
			}
			return (bool) $this->_number_of_projects;
		}
		
		public function getNumberOfProjects()
		{
			return $this->_number_of_projects;
		}
		
	}
