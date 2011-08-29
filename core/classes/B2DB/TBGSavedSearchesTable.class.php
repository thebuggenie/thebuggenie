<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	class TBGSavedSearchesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'savedsearches';
		const ID = 'savedsearches.id';
		const SCOPE = 'savedsearches.scope';
		const NAME = 'savedsearches.name';
		const DESCRIPTION = 'savedsearches.description';
		const GROUPBY = 'savedsearches.groupby';
		const GROUPORDER = 'savedsearches.grouporder';
		const ISSUES_PER_PAGE = 'savedsearches.issues_per_page';
		const TEMPLATE_NAME = 'savedsearches.templatename';
		const TEMPLATE_PARAMETER = 'savedsearches.templateparameter';
		const APPLIES_TO_PROJECT = 'savedsearches.applies_to_project';
		const IS_PUBLIC = 'savedsearches.is_public';
		const UID = 'savedsearches.uid';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addVarchar(self::DESCRIPTION, 255, '');
			parent::_addBoolean(self::IS_PUBLIC);
			parent::_addVarchar(self::TEMPLATE_NAME, 200);
			parent::_addVarchar(self::TEMPLATE_PARAMETER, 200);
			parent::_addInteger(self::ISSUES_PER_PAGE, 10);
			parent::_addVarchar(self::GROUPBY, 100);
			parent::_addVarchar(self::GROUPORDER, 5);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::APPLIES_TO_PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
		}

		public function getAllSavedSearchesByUserIDAndPossiblyProjectID($user_id, $project_id = 0)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$ctn = $crit->returnCriterion(self::UID, $user_id);
			$ctn->addOr(self::UID, 0);
			$crit->addWhere($ctn);
			if ($project_id !== 0 ) 
			{
				$crit->addWhere(self::APPLIES_TO_PROJECT, $project_id);	
			}

			$retarr = array('user' => array(), 'public' => array());
			
			if ($res = $this->doSelect($crit, 'none'))
			{
				while ($row = $res->getNextRow())
				{
					$retarr[($row->get(self::UID) != 0) ? 'user' : 'public'][$row->get(self::ID)] = $row;
				}
			}

			return $retarr;
		}

		public function saveSearch($saved_search_name, $saved_search_description, $saved_search_public, $filters, $groupby, $grouporder, $ipp, $templatename, $template_parameter, $project_id, $saved_search_id = null)
		{
			$crit = $this->getCriteria();
			if ($saved_search_id !== null)
			{
				$crit->addUpdate(self::NAME, $saved_search_name);
				$crit->addUpdate(self::DESCRIPTION, $saved_search_description);
				$crit->addUpdate(self::TEMPLATE_NAME, $templatename);
				$crit->addUpdate(self::TEMPLATE_PARAMETER, $template_parameter);
				$crit->addUpdate(self::GROUPBY, $groupby);
				$crit->addUpdate(self::GROUPORDER, $grouporder);
				$crit->addUpdate(self::ISSUES_PER_PAGE, $ipp);
				$crit->addUpdate(self::APPLIES_TO_PROJECT, $project_id);
				if (TBGContext::getUser()->canCreatePublicSearches())
				{
					$crit->addUpdate(self::IS_PUBLIC, $saved_search_public);
					$crit->addUpdate(self::UID, ((bool) $saved_search_public) ? 0 : TBGContext::getUser()->getID());
				}
				else
				{
					$crit->addUpdate(self::IS_PUBLIC, 0);
					$crit->addUpdate(self::UID, TBGContext::getUser()->getID());
				}
				$crit->addUpdate(self::SCOPE, TBGContext::getScope()->getID());
				$this->doUpdateById($crit, $saved_search_id);
			}
			else
			{
				$crit->addInsert(self::NAME, $saved_search_name);
				$crit->addInsert(self::DESCRIPTION, $saved_search_description);
				$crit->addInsert(self::TEMPLATE_NAME, $templatename);
				$crit->addInsert(self::TEMPLATE_PARAMETER, $template_parameter);
				$crit->addInsert(self::GROUPBY, $groupby);
				$crit->addInsert(self::GROUPORDER, $grouporder);
				$crit->addInsert(self::ISSUES_PER_PAGE, $ipp);
				$crit->addInsert(self::APPLIES_TO_PROJECT, $project_id);
				if (TBGContext::getUser()->canCreatePublicSearches())
				{
					$crit->addInsert(self::IS_PUBLIC, $saved_search_public);
					$crit->addInsert(self::UID, ((bool) $saved_search_public) ? 0 : TBGContext::getUser()->getID());
				}
				else
				{
					$crit->addInsert(self::IS_PUBLIC, 0);
					$crit->addUpdate(self::UID, TBGContext::getUser()->getID());
				}
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$saved_search_id = $this->doInsert($crit)->getInsertID();
			}
			Core::getTable('TBGSavedSearchFiltersTable')->deleteBySearchID($saved_search_id);
			Core::getTable('TBGSavedSearchFiltersTable')->saveFiltersForSavedSearch($saved_search_id, $filters);
			return $saved_search_id;
		}

	}
