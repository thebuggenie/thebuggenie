<?php

	/**
	 * Projects table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Projects table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tProjects extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_projects';
		const ID = 'bugs2_projects.id';
		const SCOPE = 'bugs2_projects.scope';
		const NAME = 'bugs2_projects.name';
		const PREFIX = 'bugs2_projects.prefix';
		const USE_PREFIX = 'bugs2_projects.use_prefix';
		const HOMEPAGE = 'bugs2_projects.homepage';
		const OWNED_BY = 'bugs2_projects.owned_by';
		const OWNED_TYPE = 'bugs2_projects.owned_type';
		const LEAD_BY = 'bugs2_projects.lead_by';
		const LEAD_TYPE = 'bugs2_projects.lead_type';
		const TIME_UNIT = 'bugs2_projects.time_unit';
		const HRS_PR_DAY = 'bugs2_projects.hrs_pr_day';
		const DESCRIPTION = 'bugs2_projects.description';
		const DOC_URL = 'bugs2_projects.doc_url';
		const ENABLE_TASKS = 'bugs2_projects.enable_tasks';
		const IS_DEFAULT = 'bugs2_projects.is_default';
		const DEFAULT_STATUS = 'bugs2_projects.default_status';
		const ENABLE_BUILDS = 'bugs2_projects.enable_builds';
		const ENABLE_EDITIONS = 'bugs2_projects.enable_editions';
		const ENABLE_COMPONENTS = 'bugs2_projects.enable_components';
		const SHOW_IN_SUMMARY = 'bugs2_projects.show_in_summary';
		const SUMMARY_DISPLAY = 'bugs2_projects.summary_display';
		const VOTES = 'bugs2_projects.votes';
		const QA = 'bugs2_projects.qa';
		const QA_TYPE = 'bugs2_projects.qa_type';
		const RELEASED = 'bugs2_projects.released';
		const RELEASE_DATE = 'bugs2_projects.release_date';
		const LOCKED = 'bugs2_projects.locked';
		const PLANNED_RELEASE = 'bugs2_projects.planned_release';
		const DELETED = 'bugs2_projects.deleted';
		const ALLOW_CHANGING_WITHOUT_WORKING = 'bugs2_projects.allow_changing_wo_working';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::PREFIX, 5, '');
			parent::_addBoolean(self::USE_PREFIX);
			parent::_addVarchar(self::HOMEPAGE, 200, '');
			parent::_addInteger(self::OWNED_BY, 10);
			parent::_addInteger(self::OWNED_TYPE, 3);
			parent::_addInteger(self::LEAD_BY, 10);
			parent::_addInteger(self::LEAD_TYPE, 3);
			parent::_addInteger(self::TIME_UNIT, 3);
			parent::_addInteger(self::HRS_PR_DAY, 2);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addVarchar(self::DOC_URL, 200, '');
			parent::_addBoolean(self::ENABLE_TASKS);
			parent::_addBoolean(self::IS_DEFAULT);
			parent::_addBoolean(self::ALLOW_CHANGING_WITHOUT_WORKING);
			parent::_addForeignKeyColumn(self::DEFAULT_STATUS, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
			parent::_addBoolean(self::ENABLE_BUILDS);
			parent::_addBoolean(self::ENABLE_EDITIONS);
			parent::_addBoolean(self::ENABLE_COMPONENTS);
			parent::_addBoolean(self::SHOW_IN_SUMMARY, true);
			parent::_addVarchar(self::SUMMARY_DISPLAY, 15, 'issuetypes');
			parent::_addBoolean(self::VOTES);
			parent::_addInteger(self::QA, 10);
			parent::_addInteger(self::QA_TYPE, 3);
			parent::_addBoolean(self::RELEASED);
			parent::_addInteger(self::RELEASE_DATE, 10);
			parent::_addBoolean(self::LOCKED);
			parent::_addBoolean(self::PLANNED_RELEASE);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addBoolean(self::DELETED);
		}
		
		public function clearDefaults()
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::IS_DEFAULT, false);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doUpdate($crit);
		}
		
		public function setDefaultProject($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::IS_DEFAULT, true);
			$res = $this->doUpdateById($crit, $project_id);
		}
		
		public function createNew($name, $p_id = null)
		{
			$crit = $this->getCriteria();
			if ($p_id !== null)
			{
				$crit->addInsert(self::ID, $p_id);
			}
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
		public function getByPrefix($prefix)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PREFIX, $prefix);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$row = B2DB::getTable('B2tProjects')->doSelectOne($crit);
			return $row;
		}
		
		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addOrderBy(self::NAME, B2DBCriteria::SORT_ASC);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getAllSortedByIsDefault()
		{
			$crit = $this->getCriteria();
			$crit->addOrderBy(self::IS_DEFAULT, B2DBCriteria::SORT_DESC);
			$crit->addOrderBy(self::ID, B2DBCriteria::SORT_DESC);
			$crit->addWhere(self::LOCKED, false);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$row = $this->doSelectById($id, $crit);
			return $row;
		}
		
	}
