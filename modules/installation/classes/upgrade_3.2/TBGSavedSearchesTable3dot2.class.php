<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * @Table(name="savedsearches")
	 * @Entity(class="TBGSavedSearch")
	 */
	class TBGSavedSearchesTable3dot2 extends TBGB2DBTable
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

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addBoolean(self::IS_PUBLIC);
			parent::_addVarchar(self::TEMPLATE_NAME, 200);
			parent::_addVarchar(self::TEMPLATE_PARAMETER, 200);
			parent::_addInteger(self::ISSUES_PER_PAGE, 10);
			parent::_addVarchar(self::GROUPBY, 100);
			parent::_addVarchar(self::GROUPORDER, 5);
			parent::_addForeignKeyColumn(self::APPLIES_TO_PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
