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
	class BUGSissuetype extends BUGSdatatype 
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
		 * @return BUGSissuetype
		 */
		public static function createNew($name, $icon = 'bug_report')
		{
			$res = B2DB::getTable('B2tIssueTypes')->createNew($name, $icon);
			return BUGSfactory::BUGSissuetypeLab($res->getInsertID());
		}

		/**
		 * Return an array of available icons
		 *
		 * @return array
		 */
		public static function getIcons()
		{
			$i18n = BUGScontext::getI18n();
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
					$row = B2DB::getTable('B2tIssueTypes')->doSelectById($i_id);
				}
				if ($row instanceof B2DBRow)
				{
					$this->_itemid = $i_id;
					$this->_appliesto = ($row->get(B2tIssueTypes::APPLIES_TO) != 0) ? BUGSfactory::projectLab($row->get(B2tIssueTypes::APPLIES_TO)) : null;
					$this->_itemdata = $row->get(B2tIssueTypes::ICON);
					$this->_description = $row->get(B2tIssueTypes::DESCRIPTION);
					$this->_reportable = (bool) $row->get(B2tIssueTypes::IS_REPORTABLE);
					$this->_itemtype = BUGSdatatype::ISSUETYPE;
					$this->_name = $row->get(B2tIssueTypes::NAME);
					$this->_istask = (bool) $row->get(B2tIssueTypes::IS_TASK);
					$this->_redirect_after_reporting = (bool) $row->get(B2tIssueTypes::REDIRECT_AFTER_REPORTING);
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
				$this->_visiblefields = B2DB::getTable('B2tIssueFields')->getVisibleFieldsArrayByIssuetypeID($this->getID());
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
			B2DB::getTable('B2tIssueFields')->deleteByIssuetypeID($this->getID());
		}

		public function setFieldAvailable($key, $details)
		{
			B2DB::getTable('B2tIssueFields')->addFieldAndDetailsByIssuetypeID($this->getID(), $key, $details);
		}

		public function save()
		{
			B2DB::getTable('B2tIssueTypes')->saveDetails($this);
		}

		static function getTask()
		{
			try
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tIssueTypes::IS_TASK, 1);
				$crit->addWhere(B2tIssueTypes::SCOPE, BUGScontext::getScope()->getID());
				return B2DB::getTable('B2tIssueTypes')->doSelectOne($crit)->get(B2tIssueTypes::ID);
			}
			catch (Exception $e)
			{
				return 0;
			}
		}
		
		/**
		 * Returns the BUGSproject which the issue type applies to, if any
		 *
		 * @return BUGSproject
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
					$ctn = $crit->returnCriterion(B2tIssueTypes::APPLIES_TO, $project_id);
					$ctn->addOr(B2tIssueTypes::APPLIES_TO, 0);
					$crit->addWhere($ctn);
				}
				else
				{
					$crit->addWhere(B2tIssueTypes::APPLIES_TO, 0);
				}
				$crit->addWhere(B2tIssueTypes::SCOPE, BUGScontext::getScope()->getID());
				$crit->addOrderBy(B2tIssueTypes::ID, B2DBCriteria::SORT_ASC);
		
				$res = B2DB::getTable('B2tIssueTypes')->doSelect($crit);
				while ($row = $res->getNextRow())
				{
					$issuetypes[$row->get(B2tIssueTypes::ID)] = BUGSfactory::BUGSissuetypeLab($res->get(B2tIssueTypes::ID), $row);
				}
		
				self::$_issuetypes[$project_id] = $issuetypes;
			}
			return self::$_issuetypes[$project_id];
		}		
	}
	