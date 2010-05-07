<?php

	/**
	 * Issue type class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
		/**
		 * If true, is the default issue type when promoting tasks to issues
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $_istask;
		
		protected $_appliesto;
		
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
			$res = B2DB::getTable('TBGIssueTypesTable')->createNew($name, $icon);
			return TBGFactory::TBGIssuetypeLab($res->getInsertID());
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

		/**
		 * Constructor function
		 *
		 * @param integer $i_id
		 * @param integer $item_type Must always be ISSUETYPE
		 */
		public function __construct($i_id, $row = null)
		{
			try
			{
				if ($row === null)
				{
					$row = B2DB::getTable('TBGIssueTypesTable')->doSelectById($i_id);
				}
				if ($row instanceof B2DBRow)
				{
					$this->_itemid = $i_id;
					$this->_appliesto = ($row->get(TBGIssueTypesTable::APPLIES_TO) != 0) ? TBGFactory::projectLab($row->get(TBGIssueTypesTable::APPLIES_TO)) : null;
					$this->_itemdata = $row->get(TBGIssueTypesTable::ICON);
					$this->_description = $row->get(TBGIssueTypesTable::DESCRIPTION);
					$this->_reportable = (bool) $row->get(TBGIssueTypesTable::IS_REPORTABLE);
					$this->_itemtype = TBGDatatype::ISSUETYPE;
					$this->_name = $row->get(TBGIssueTypesTable::NAME);
					$this->_istask = ($row->get(TBGIssueTypesTable::ICON) == 'task') ? true : false;
					$this->_redirect_after_reporting = (bool) $row->get(TBGIssueTypesTable::REDIRECT_AFTER_REPORTING);
				}
				else
				{
					throw new Exception('This issue type does not exist');
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
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
			return (bool) $this->_istask;
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

		public function save()
		{
			B2DB::getTable('TBGIssueTypesTable')->saveDetails($this);
		}

		static function getTask()
		{
			try
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGIssueTypesTable::ICON, 'task');
				$crit->addWhere(TBGIssueTypesTable::SCOPE, TBGContext::getScope()->getID());
				$row = B2DB::getTable('TBGIssueTypesTable')->doSelectOne($crit);
				if ($row instanceof B2DBRow)
				{
					return TBGFactory::TBGIssuetypeLab($row->get(TBGIssueTypesTable::ID), $row);
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
			return $this->_appliesto;
		}
		
		/**
		 * Returns whether or not the issue type applies to a project
		 *
		 * @return boolean
		 */
		public function appliesToProject()
		{
			return ($this->_appliesto == null) ? false : true;
		}
		
		public static function getAllApplicableToProject($p_id)
		{
			return self::getAll($p_id);
		}
		
		/**
		 * Returns an array of issue types
		 *
		 * @param integer $project_id  The id of the project which this issue type applies to 
		 * @return array
		 */
		public static function getAll($project_id = 0)
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
				$crit->addWhere(TBGIssueTypesTable::SCOPE, TBGContext::getScope()->getID());
				$crit->addOrderBy(TBGIssueTypesTable::ID, B2DBCriteria::SORT_ASC);
		
				$res = B2DB::getTable('TBGIssueTypesTable')->doSelect($crit);
				while ($row = $res->getNextRow())
				{
					$issuetypes[$row->get(TBGIssueTypesTable::ID)] = TBGFactory::TBGIssuetypeLab($res->get(TBGIssueTypesTable::ID), $row);
				}
		
				self::$_issuetypes[$project_id] = $issuetypes;
			}
			return self::$_issuetypes[$project_id];
		}		
	}
	