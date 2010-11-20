<?php

	/**
	 * Projects table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGProjectsTable extends TBGB2DBTable 
	{
		const B2DBNAME = 'projects';
		const ID = 'projects.id';
		const SCOPE = 'projects.scope';
		const NAME = 'projects.name';
		const KEY = 'projects.key';
		const PREFIX = 'projects.prefix';
		const USE_PREFIX = 'projects.use_prefix';
		const USE_SCRUM = 'projects.use_scrum';
		const HOMEPAGE = 'projects.homepage';
		const OWNER = 'projects.owner';
		const OWNER_TYPE = 'projects.owner_type';
		const LEAD_BY = 'projects.leader';
		const LEAD_TYPE = 'projects.leader_type';
		const DESCRIPTION = 'projects.description';
		const DOC_URL = 'projects.doc_url';
		const ENABLE_BUILDS = 'projects.enable_builds';
		const ENABLE_EDITIONS = 'projects.enable_editions';
		const ENABLE_COMPONENTS = 'projects.enable_components';
		const SHOW_IN_SUMMARY = 'projects.show_in_summary';
		const SUMMARY_DISPLAY = 'projects.summary_display';
		const QA = 'projects.qa_responsible';
		const QA_TYPE = 'projects.qa_responsible_type';
		const LOCKED = 'projects.locked';
		const DELETED = 'projects.deleted';
		const ALLOW_CHANGING_WITHOUT_WORKING = 'projects.allow_freelancing';
		const WORKFLOW_SCHEME_ID = 'projects.workflow_scheme_id';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGProjectsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGProjectsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::KEY, 100);
			parent::_addVarchar(self::PREFIX, 5, '');
			parent::_addBoolean(self::USE_PREFIX);
			parent::_addBoolean(self::USE_SCRUM);
			parent::_addVarchar(self::HOMEPAGE, 200, '');
			parent::_addInteger(self::OWNER, 10);
			parent::_addInteger(self::OWNER_TYPE, 3);
			parent::_addInteger(self::LEAD_BY, 10);
			parent::_addInteger(self::LEAD_TYPE, 3);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addVarchar(self::DOC_URL, 200, '');
			parent::_addBoolean(self::ALLOW_CHANGING_WITHOUT_WORKING);
			parent::_addBoolean(self::ENABLE_BUILDS);
			parent::_addBoolean(self::ENABLE_EDITIONS);
			parent::_addBoolean(self::ENABLE_COMPONENTS);
			parent::_addBoolean(self::SHOW_IN_SUMMARY, true);
			parent::_addVarchar(self::SUMMARY_DISPLAY, 15, 'issuetypes');
			parent::_addInteger(self::QA, 10);
			parent::_addInteger(self::QA_TYPE, 3);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_SCHEME_ID, TBGWorkflowSchemesTable::getTable(), TBGWorkflowSchemesTable::ID);
			parent::_addBoolean(self::DELETED);
		}
		
		public function clearDefaults()
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::IS_DEFAULT, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
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
			$crit->addInsert(self::KEY, strtolower(str_replace(' ', '', $name)));
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::WORKFLOW_SCHEME_ID, 1);
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
		public function getByPrefix($prefix)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PREFIX, $prefix);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectOne($crit);
			return $row;
		}
		
		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addOrderBy(self::NAME, B2DBCriteria::SORT_ASC);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
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
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectById($id, $crit, false);
			return $row;
		}
		
		public function getByKey($key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::KEY, $key);
			$row = $this->doSelectOne($crit, false);
			return $row;
		}
	}
