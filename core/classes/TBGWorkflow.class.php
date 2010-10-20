<?php

	/**
	 * Workflow class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGWorkflow extends TBGIdentifiableClass
	{

		protected static $_workflows = null;

		/**
		 * The workflow description
		 *
		 * @var string
		 */
		protected $_description = null;

		/**
		 * Whether the workflow is active or not
		 *
		 * @var boolean
		 */
		protected $_is_active = null;

		protected $_steps = null;

		protected $_num_steps = null;

		/**
		 * Return all workflows in the system
		 *
		 * @return array An array of TBGWorkflow objects
		 */
		public static function getAll()
		{
			if (self::$_workflows === null)
			{
				self::$_workflows = array();
				if ($res = TBGWorkflowsTable::getTable()->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_workflows[$row->get(TBGWorkflowsTable::ID)] = TBGContext::factory()->TBGWorkflow($row->get(TBGWorkflowsTable::ID), $row);
					}
				}
			}
			return self::$_workflows;
		}

		public function __construct($id, $row)
		{
			if (!is_numeric($id))
			{
				throw new Exception('Please specify a valid workflow id');
			}
			if ($row === null)
			{
				$row = TBGWorkflowsTable::getTable()->getByID($id);
			}

			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified file id does not exist');
			}

			$this->_itemid = $row->get(TBGWorkflowsTable::ID);
			$this->_name = $row->get(TBGWorkflowsTable::NAME);
			$this->_description = $row->get(TBGWorkflowsTable::DESCRIPTION);
			$this->_is_active = (bool) $row->get(TBGWorkflowsTable::IS_ACTIVE);
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

		/**
		 * Whether this is the builtin workflow that cannot be edited or removed
		 *
		 * @return boolean
		 */
		public function isActive()
		{
			return (bool) $this->_is_active;
		}

		protected function _populateSteps()
		{
			if ($this->_steps === null)
			{
				$this->_steps = TBGWorkflowStepsTable::getTable()->getByWorkflowID($this->getID());
			}
		}

		/**
		 * Get all steps in this workflow
		 *
		 * @return array An array of TBGWorkflowStep objects
		 */
		public function getSteps()
		{
			$this->_populateSteps();
			return $this->_steps;
		}

		public function getNumberOfSteps()
		{
			if ($this->_num_steps === null)
			{
				$this->_num_steps = TBGWorkflowStepsTable::getTable()->countByWorkflowID($this->getID());
			}
			return $this->_num_steps;
		}

	}
