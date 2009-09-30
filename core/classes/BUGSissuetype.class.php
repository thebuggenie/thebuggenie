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
		
		static $_issuetypes = array();
		
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
		
		/**
		 * Returns whether or not this issue type is the default for promoting tasks to issues
		 *
		 * @return boolean
		 * @access public
		 */
		public function isTask()
		{
			return $this->_istask;
		}
		
		public function getIcon()
		{
			return $this->_itemdata;
		}
		
		public function getDescription()
		{
			return $this->_description;
		}
		
		public function getRedirectAfterReporting()
		{
			return $this->_redirect_after_reporting;
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
		
		public function isDefaultForIssues()
		{
			return (BUGSsettings::get('defaultissuetypefornewissues') == $this->getID()) ? true : false;
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
	