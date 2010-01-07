<?php

	class B2tSavedSearches extends B2DBTable 
	{
		const B2DBNAME = 'savedsearches';
		const ID = 'savedsearches.id';
		const SCOPE = 'savedsearches.scope';
		const NAME = 'savedsearches.name';
		const DESCRIPTION = 'savedsearches.description';
		const GROUPBY = 'savedsearches.groupby';
		const GROUPORDER = 'savedsearches.grouporder';
		const ISSUES_PER_PAGE = 'savedsearches.issues_per_page';
		const TEMPLATE_NAME = 'savedsearches.templatename';
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
			parent::_addInteger(self::ISSUES_PER_PAGE, 10);
			parent::_addVarchar(self::GROUPBY, 100);
			parent::_addVarchar(self::GROUPORDER, 5);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::APPLIES_TO_PROJECT, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
		}

		public function getAllSavedSearchesByUserIDAndPossiblyProjectID($user_id, $project_id = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$ctn = $crit->returnCriterion(self::UID, $user_id);
			$ctn->addOr(self::UID, 0);
			$crit->addWhere($ctn);
			
			if ($project_id !== null)
			{
				$crit->addWhere(self::APPLIES_TO_PROJECT, $project_id);
			}

			$retarr = array('user' => array(), 'public' => array());
			
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$retarr[($row->get(self::UID) != 0) ? 'user' : 'public'][$row->get(self::ID)] = $row;
				}
			}

			return $retarr;
		}

	}
?>