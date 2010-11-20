<?php

	/**
	 * Issue type class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Issue type class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGIssuetype extends TBGDatatype 
	{
		
		protected $_b2dbtablename = 'TBGIssueTypesTable';
		
		/**
		 * If true, is the default issue type when promoting tasks to issues
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $_task;
		
		protected $_itemtype = TBGDatatype::ISSUETYPE;
		
		protected $_applies_to;
		
		protected $_description;
		
		protected $_redirect_after_reporting = true;

		protected $_reportable = null;
		
		static $_issuetypes = array();

		protected $_visiblefields = null;

		/**
		 * Create a new issue type and return it
		 *
		 * @param string $name
		 * @param string $icon
		 *
		 * @return TBGIssuetype
		 */
		public static function createNew($name, $icon = 'bug_report')
		{
			$res = TBGIssueTypesTable::getTable()->createNew($name, $icon);
			return TBGContext::factory()->TBGIssuetype($res->getInsertID());
		}

		/**
		 * Return an array of available icons
		 *
		 * @return array
		 */
		public static function getIcons()
		{
			$i18n = TBGContext::getI18n();
			$icons = array();
			$icons['bug_report'] = $i18n->__('Bug report');
			$icons['documentation_request'] = $i18n->__('Documentation request');
			$icons['enhancement'] = $i18n->__('Enhancement');
			$icons['feature_request'] = $i18n->__('Feature request');
			$icons['idea'] = $i18n->__('Idea');
			$icons['support_request'] = $i18n->__('Support request');
			$icons['task'] = $i18n->__('Task');
			$icons['developer_report'] = $i18n->__('User story');
			
			return $icons;
		}

		public static function getIssuetypeByKeyish($key)
		{
			foreach (self::getAll() as $issuetype)
			{
				if (str_replace(' ', '', strtolower($issuetype->getName())) == str_replace(' ', '', strtolower($key)))
				{
					return $issuetype;
				}
			}
			return null;
		}

		protected function _populateVisibleFields()
		{
			if ($this->_visiblefields === null)
			{
				$this->_visiblefields = B2DB::getTable('TBGIssueFieldsTable')->getVisibleFieldsArrayByIssuetypeID($this->getID());
			}
		}

		public function getVisibleFields()
		{
			$this->_populateVisibleFields();
			return $this->_visiblefields;
		}

		/**
		 * Whether a field is visible for this issue type
		 *
		 * @param string $key
		 *
		 * @return boolean
		 */
		public function isFieldVisible($key)
		{
			$visiblefields = $this->getVisibleFields();
			return array_key_exists($key, $visiblefields);
		}
		
		/**
		 * Returns whether or not this issue type is the default for promoting tasks to issues
		 *
		 * @return boolean
		 */
		public function isTask()
		{
			return (bool) $this->_task;
		}

		/**
		 * Set whether or not this issue type is reportable
		 *
		 * @param boolean $val
		 *
		 * @return boolean
		 */
		public function setIsReportable($val)
		{
			$this->_reportable = (bool) $val;
		}

		/**
		 * Returns or set whether or not this issue type is reportable
		 *
		 * @param boolean[optional] $val Provide a value to set it
		 *
		 * @return boolean
		 */
		public function isReportable($val = null)
		{
			if ($val !== null)
			{
				$this->setIsReportable($val);
			}
			return (bool) $this->_reportable;
		}

		public function getIcon()
		{
			return $this->_itemdata;
		}

		public function setIcon($icon)
		{
			$this->_itemdata = $icon;
		}
		
		public function getDescription()
		{
			return $this->_description;
		}

		public function setDescription($description)
		{
			$this->_description = $description;
		}
		
		public function getRedirectAfterReporting()
		{
			return $this->_redirect_after_reporting;
		}

		public function setRedirectAfterReporting($val)
		{
			$this->_redirect_after_reporting = (bool) $val;
		}

		public function clearAvailableFields()
		{
			B2DB::getTable('TBGIssueFieldsTable')->deleteByIssuetypeID($this->getID());
		}

		public function setFieldAvailable($key, $details)
		{
			B2DB::getTable('TBGIssueFieldsTable')->addFieldAndDetailsByIssuetypeID($this->getID(), $key, $details);
		}

		static function getTask()
		{
			try
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGIssueTypesTable::ICON, 'task');
				$crit->addWhere(TBGIssueTypesTable::SCOPE, TBGContext::getScope()->getID());
				$row = TBGIssueTypesTable::getTable()->doSelectOne($crit);
				if ($row instanceof B2DBRow)
				{
					return TBGContext::factory()->TBGIssuetype($row->get(TBGIssueTypesTable::ID), $row);
				}
				else
				{
					throw new Exception("Couldn't find any 'task' issue types");
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		/**
		 * Returns the TBGProject which the issue type applies to, if any
		 *
		 * @return TBGProject
		 */
		public function getAppliesTo()
		{
			return $this->_applies_to;
		}
		
		/**
		 * Returns whether or not the issue type applies to a project
		 *
		 * @return boolean
		 */
		public function appliesToProject()
		{
			return ($this->_applies_to == null) ? false : true;
		}
		
		public static function getAllApplicableToProject($p_id)
		{
			return self::getAll($p_id);
		}
		
		/**
		 * Returns an array of issue types
		 *
		 * @param integer $project_id  The id of the project which this issue type applies to 
		 * @param integer $scope_id  The ID number of the scope to load issue types from
		 * @return array
		 */
		public static function getAll($project_id = 0, $scope_id = null)
		{
			if (!array_key_exists($project_id, self::$_issuetypes))
			{
				$issuetypes = array();
				$crit = new B2DBCriteria();
				$crit->setDistinct();
		
				if ($project_id !== 0)
				{
					$ctn = $crit->returnCriterion(TBGIssueTypesTable::APPLIES_TO, $project_id);
					$ctn->addOr(TBGIssueTypesTable::APPLIES_TO, 0);
					$crit->addWhere($ctn);
				}
				else
				{
					$crit->addWhere(TBGIssueTypesTable::APPLIES_TO, 0);
				}

				if ($scope_id === null)
				{
					$crit->addWhere(TBGIssueTypesTable::SCOPE, TBGContext::getScope()->getID());
				}
				else
				{
					$crit->addWhere(TBGIssueTypesTable::SCOPE, $scope_id);
				}

				$crit->addOrderBy(TBGIssueTypesTable::ID, B2DBCriteria::SORT_ASC);
		
				$res = TBGIssueTypesTable::getTable()->doSelect($crit);
				while ($row = $res->getNextRow())
				{
					$issuetypes[$row->get(TBGIssueTypesTable::ID)] = TBGContext::factory()->TBGIssuetype($res->get(TBGIssueTypesTable::ID), $row);
				}
		
				self::$_issuetypes[$project_id] = $issuetypes;
			}
			return self::$_issuetypes[$project_id];
		}
	}
	
