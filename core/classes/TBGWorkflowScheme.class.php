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

		protected static $_schemes = null;

		protected $_issuetype_workflows = null;

		protected $_num_issuetype_workflows = null;

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

		public function __construct($id, $row)
		{
			if (!is_numeric($id))
			{
				throw new Exception('Please specify a valid workflow scheme id');
			}
			if ($row === null)
			{
				$row = TBGWorkflowSchemesTable::getTable()->getByID($id);
			}

			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified workflow scheme id does not exist');
			}

			$this->_itemid = $row->get(TBGWorkflowSchemesTable::ID);
			$this->_name = $row->get(TBGWorkflowSchemesTable::NAME);
			$this->_description = $row->get(TBGWorkflowSchemesTable::DESCRIPTION);
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

	}
