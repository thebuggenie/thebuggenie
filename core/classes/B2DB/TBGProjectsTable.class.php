<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Projects table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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

		const B2DB_TABLE_VERSION = 1;
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
		const CLIENT = 'projects.client';
		const DESCRIPTION = 'projects.description';
		const DOC_URL = 'projects.doc_url';
		const RELEASED = 'projects.isreleased';
		const PLANNED_RELEASED = 'projects.isplannedreleased';
		const RELEASE_DATE = 'projects.release_date';
		const ENABLE_BUILDS = 'projects.enable_builds';
		const ENABLE_EDITIONS = 'projects.enable_editions';
		const ENABLE_COMPONENTS = 'projects.enable_components';
		const SHOW_IN_SUMMARY = 'projects.show_in_summary';
		const SUMMARY_DISPLAY = 'projects.summary_display';
		const HAS_DOWNLOADS = 'projects.has_downloads';
		const QA = 'projects.qa_responsible';
		const QA_TYPE = 'projects.qa_responsible_type';
		const LOCKED = 'projects.locked';
		const DELETED = 'projects.deleted';
		const SMALL_ICON = 'projects.small_icon';
		const LARGE_ICON = 'projects.large_icon';
		const ALLOW_CHANGING_WITHOUT_WORKING = 'projects.allow_freelancing';
		const WORKFLOW_SCHEME_ID = 'projects.workflow_scheme_id';
		const ISSUETYPE_SCHEME_ID = 'projects.issuetype_scheme_id';
		const AUTOASSIGN = 'projects.autoassign';
		const PARENT_PROJECT_ID = 'projects.parent';
		const ARCHIVED = 'projects.archived';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::KEY, 100);
			parent::_addVarchar(self::PREFIX, 5, '');
			parent::_addBoolean(self::USE_PREFIX);
			parent::_addBoolean(self::USE_SCRUM);
			parent::_addBoolean(self::HAS_DOWNLOADS);
			parent::_addVarchar(self::HOMEPAGE, 200, '');
			parent::_addInteger(self::OWNER, 10);
			parent::_addInteger(self::OWNER_TYPE, 3);
			parent::_addInteger(self::LEAD_BY, 10);
			parent::_addInteger(self::LEAD_TYPE, 3);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addVarchar(self::DOC_URL, 200, '');
			parent::_addBoolean(self::ALLOW_CHANGING_WITHOUT_WORKING);
			parent::_addBoolean(self::RELEASED);
			parent::_addBoolean(self::PLANNED_RELEASED);
			parent::_addInteger(self::RELEASE_DATE, 10);
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
			parent::_addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, TBGIssuetypeSchemesTable::getTable(), TBGIssuetypeSchemesTable::ID);
			parent::_addForeignKeyColumn(self::CLIENT, TBGClientsTable::getTable(), TBGClientsTable::ID);
			parent::_addForeignKeyColumn(self::PARENT_PROJECT_ID, $this, self::ID);
			parent::_addForeignKeyColumn(self::SMALL_ICON, TBGFilesTable::getTable(), TBGFilesTable::ID);
			parent::_addForeignKeyColumn(self::LARGE_ICON, TBGFilesTable::getTable(), TBGFilesTable::ID);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::AUTOASSIGN);
			parent::_addBoolean(self::ARCHIVED);
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
			$crit->addInsert(self::KEY, mb_strtolower(str_replace(' ', '', $name)));
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
			$crit->addOrderBy(self::NAME, Criteria::SORT_ASC);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getAllSortedByIsDefault()
		{
			$crit = $this->getCriteria();
			$crit->addOrderBy(self::IS_DEFAULT, Criteria::SORT_DESC);
			$crit->addOrderBy(self::ID, Criteria::SORT_DESC);
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
		
		public function getByClientID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::CLIENT, $id);
			$row = $this->doSelect($crit, false);
			return $row;
		}
		
		public function getByParentID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::PARENT_PROJECT_ID, $id);
			$row = $this->doSelect($crit, false);
			return $row;
		}
		
		public function getByKey($key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::KEY, $key);
			$crit->addWhere(self::KEY, '', Criteria::DB_NOT_EQUALS);
			$row = $this->doSelectOne($crit, false);
			return $row;
		}
		
		public function countByIssuetypeSchemeID($scheme_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::ARCHIVED, false);
			
			return $this->doCount($crit);
		}
		
		public function countByWorkflowSchemeID($scheme_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::WORKFLOW_SCHEME_ID, $scheme_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::ARCHIVED, false);
			
			return $this->doCount($crit);
		}
		
		public function countProjects($scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::ARCHIVED, false);

			return $this->doCount($crit);
		}

		public function getByUserID($user_id)
		{
			$crit = $this->getCriteria();
			$ctn = $crit->returnCriterion(self::LEAD_BY, $user_id);
			$ctn->addWhere(self::LEAD_TYPE, TBGIdentifiableClass::TYPE_USER);
			$ctn->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere($ctn);
			$ctn = $crit->returnCriterion(self::OWNER, $user_id);
			$ctn->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$ctn->addWhere(self::OWNER_TYPE, TBGIdentifiableClass::TYPE_USER);
			$crit->addOr($ctn);
			
			$return_array = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$return_array[$row->get(self::ID)] = TBGContext::factory()->TBGProject($row->get(self::ID), $row);
				}
			}
			return $return_array;
		}

		public function updateByIssuetypeSchemeID($scheme_id)
		{
			$schemes = TBGIssuetypeScheme::getAll();
			foreach ($schemes as $default_scheme_id => $scheme)
			{
				break;
			}
			
			$crit = $this->getCriteria();
			
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addUpdate(self::ISSUETYPE_SCHEME_ID, $default_scheme_id);
			
			$res = $this->doUpdate($crit);
		}
		
	}
