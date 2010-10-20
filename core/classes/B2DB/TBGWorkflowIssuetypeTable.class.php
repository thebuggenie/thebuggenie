<?php

	/**
	 * Link table between workflow and issue type
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Link table between workflow and issue type
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGWorkflowIssuetypeTable extends B2DBTable
	{

		const B2DBNAME = 'workflow_issuetype';
		const ID = 'workflow_issuetype.id';
		const SCOPE = 'workflow_issuetype.scope';
		const WORKFLOW_SCHEME_ID = 'workflow_issuetype.workflow_scheme_id';
		const WORKFLOW_ID = 'workflow_issuetype.workflow_id';
		const ISSUETYPE_ID = 'workflow_issuetype.issutype_id';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowIssuetypeTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGWorkflowIssuetypeTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_SCHEME_ID, TBGWorkflowSchemesTable::getTable(), TBGWorkflowSchemesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_ID, TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID);
		}

	}